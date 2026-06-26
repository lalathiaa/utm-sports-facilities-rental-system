<?php

namespace App\Services;

use App\Contracts\AiExplainerInterface;
use App\Models\Booking;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * RecommendationService — Layer 1 (Statistical Engine)
 * =====================================================
 * Computes ranked (facility, timeslot) recommendations for a given user.
 *
 * SCORING FORMULA
 * ───────────────
 * For every (facility, slot) candidate pair that is currently bookable, we compute:
 *
 *   score = W_RATING    × normalised_avg_rating       (0–1)
 *         + W_POPULARITY × normalised_global_popularity (0–1)
 *         + W_PERSONAL   × normalised_personal_affinity (0–1)
 *         + W_SLOT       × normalised_slot_demand       (0–1)
 *
 *   final_score = score × 10   (displayed as x.x / 10)
 *
 * Component definitions:
 *   - normalised_avg_rating      : facility.averageRating() / 5.0
 *   - normalised_global_popularity: this facility's all-time confirmed booking count
 *                                   ÷ max booking count across all facilities.
 *                                   New/unbooked facility → 0; most-booked facility → 1.
 *   - normalised_personal_affinity: user's confirmed bookings at this facility
 *                                   ÷ user's total confirmed bookings.
 *                                   Cold-start (0 bookings) → 0 for all → gracefully ignored.
 *   - normalised_slot_demand      : how often this hour was booked system-wide
 *                                   ÷ max bookings at any single hour.
 *
 * AVAILABILITY FILTER
 * ───────────────────
 * Slots in Facility::unavailableSlotsOn($date) are excluded before scoring.
 * We look ahead up to LOOKAHEAD_PRIMARY_DAYS days for the first available slot,
 * falling back to LOOKAHEAD_FALLBACK_DAYS if nothing is found in the primary window.
 *
 * COLD-START
 * ──────────
 * A user with zero booking history gets W_PERSONAL = 0 implicitly (affinity is 0).
 * The remaining three components (rating, popularity, slot demand) still produce a
 * sensible popularity + quality ranking — never an empty page or an error.
 */
class RecommendationService
{
    // ─── Scoring weights (must sum to 1.0) ──────────────────────────────────

    private const W_RATING     = 0.35;
    private const W_POPULARITY = 0.30;
    private const W_PERSONAL   = 0.25;
    private const W_SLOT       = 0.10;

    // ─── Availability look-ahead windows (days) ──────────────────────────────

    private const LOOKAHEAD_PRIMARY_DAYS  = 7;
    private const LOOKAHEAD_FALLBACK_DAYS = 14;

    // ─── All valid slot start times (mirrors BookingController::SLOT_HOURS) ─

    private const SLOT_HOURS = [
        '08:00','09:00','10:00','11:00','12:00',
        '13:00','14:00','15:00','16:00','17:00',
        '18:00','19:00','20:00','21:00',
    ];

    public function __construct(private readonly AiExplainerInterface $explainer) {}

    /**
     * Return the top $limit recommended (facility, slot) pairs for a given user.
     *
     * @param  int $userId
     * @param  int $limit  Number of recommendations to return
     * @return Collection<int, array>  Each item: [facility, slot_date, slot_start, score, reason]
     */
    public function recommend(int $userId, int $limit = 5): Collection
    {
        // ── 1. Load all available facilities ────────────────────────────────
        $facilities = Facility::with(['feedbacks', 'bookings'])->where('status', 'available')->get();

        if ($facilities->isEmpty()) {
            return collect();
        }

        // ── 2. Pre-compute global signals ───────────────────────────────────

        // Global booking count per facility (confirmed only — these represent actual demand)
        $globalCounts = Booking::whereIn('status', ['confirmed', 'cancel_requested'])
            ->selectRaw('facility_id, COUNT(*) as cnt')
            ->groupBy('facility_id')
            ->pluck('cnt', 'facility_id');

        $maxGlobalCount = max($globalCounts->max() ?? 1, 1);

        // Personal affinity: user's confirmed bookings per facility
        $personalCounts = Booking::where('user_id', $userId)
            ->whereIn('status', ['confirmed', 'cancel_requested'])
            ->selectRaw('facility_id, COUNT(*) as cnt')
            ->groupBy('facility_id')
            ->pluck('cnt', 'facility_id');

        $totalPersonal = max($personalCounts->sum(), 1); // avoid division by zero; cold-start safe

        // Slot-hour demand: system-wide bookings per hour (H:i format)
        $slotDemand = Booking::whereIn('status', ['confirmed', 'cancel_requested'])
            ->selectRaw("TIME_FORMAT(slot_start, '%H:%i') as hour, COUNT(*) as cnt")
            ->groupBy('hour')
            ->pluck('cnt', 'hour');

        $maxSlotDemand = max($slotDemand->max() ?? 1, 1);

        // ── 3. Find the best available slot for each facility ─────────────────
        $candidates = collect();

        foreach ($facilities as $facility) {
            $bestSlot = $this->findBestAvailableSlot($facility);
            if ($bestSlot === null) {
                continue; // no available slot in either window — skip
            }

            [$slotDate, $slotStart] = $bestSlot;

            // ── Compute normalised components ──────────────────────────────

            // (a) Rating component
            $normRating = $facility->averageRating() / 5.0;

            // (b) Global popularity component
            $normPopularity = ($globalCounts->get($facility->id, 0)) / $maxGlobalCount;

            // (c) Personal affinity component
            //     If user has zero history, $totalPersonal = 1 and $personalCounts = [],
            //     so normPersonal = 0 for all facilities — cold-start handled gracefully.
            $normPersonal = $personalCounts->get($facility->id, 0) / $totalPersonal;

            // (d) Slot demand component
            $normSlot = ($slotDemand->get($slotStart, 0)) / $maxSlotDemand;

            // ── Weighted sum ───────────────────────────────────────────────
            $rawScore = (self::W_RATING    * $normRating)
                      + (self::W_POPULARITY * $normPopularity)
                      + (self::W_PERSONAL   * $normPersonal)
                      + (self::W_SLOT       * $normSlot);

            $score = round($rawScore * 10, 1); // scale to 0–10

            $candidates->push([
                'facility'    => $facility,
                'slot_date'   => $slotDate,
                'slot_start'  => $slotStart,
                'slot_end'    => $this->nextHour($slotStart),
                'score'       => $score,

                // Raw components passed to the explainer (Layer 2)
                '_rating'     => round($facility->averageRating(), 1),
                '_popularity' => $globalCounts->get($facility->id, 0),
                '_personal'   => $personalCounts->get($facility->id, 0),
                '_slotDemand' => $slotDemand->get($slotStart, 0),
                '_isPersonal' => $personalCounts->get($facility->id, 0) > 0,
                '_isColdStart'=> $personalCounts->sum() === 0,
            ]);
        }

        // ── 4. Sort by score descending, take top $limit ─────────────────────
        $top = $candidates->sortByDesc('score')->take($limit)->values();

        // ── 5. Enrich with Layer 2 natural-language reasons ──────────────────
        return $top->map(function (array $rec) {
            $rec['reason'] = $this->explainer->explain([
                'facility_name' => $rec['facility']->name,
                'rating'        => $rec['_rating'],
                'popularity'    => $rec['_popularity'],
                'slot'          => $rec['slot_start'],
                'score'         => $rec['score'],
                'is_personal'   => $rec['_isPersonal'],
                'is_cold_start' => $rec['_isColdStart'],
                'slot_demand'   => $rec['_slotDemand'],
            ]);
            return $rec;
        });
    }

    /**
     * Find the best (date, slot_start) for a facility within the look-ahead windows.
     * Primary window: next LOOKAHEAD_PRIMARY_DAYS days.
     * Fallback window: days LOOKAHEAD_PRIMARY_DAYS+1 → LOOKAHEAD_FALLBACK_DAYS.
     *
     * For each date, we pick the first available slot (earliest hour).
     * Returns null if the facility is fully booked across both windows.
     *
     * @return array{string, string}|null  [date string 'Y-m-d', slot 'HH:MM']
     */
    private function findBestAvailableSlot(Facility $facility): ?array
    {
        $today    = Carbon::today();
        $maxDays  = self::LOOKAHEAD_FALLBACK_DAYS;

        for ($offset = 0; $offset < $maxDays; $offset++) {
            $date       = $today->copy()->addDays($offset)->toDateString();
            $unavailable = $facility->unavailableSlotsOn($date);

            foreach (self::SLOT_HOURS as $slot) {
                if (!in_array($slot, $unavailable)) {
                    return [$date, $slot];
                }
            }
        }

        return null; // fully booked across entire window
    }

    /**
     * Return the next hour string for slot_end computation.
     * '21:00' wraps to '22:00' (valid closing time).
     */
    private function nextHour(string $slotStart): string
    {
        return date('H:i', strtotime($slotStart) + 3600);
    }
}
