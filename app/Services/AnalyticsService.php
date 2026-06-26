<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * AnalyticsService — Layer 1 (Hybrid PHP + Python Analytics Engine)
 * ==================================================================
 * Architecture
 * ────────────
 * 1. PRIMARY PATH: Serialise raw booking rows to JSON and invoke
 *    scripts/analytics_engine.py via Laravel's Process facade.
 *    The Python script uses pandas for groupby/aggregation and outputs
 *    a single JSON object on stdout matching this class's return shape.
 *
 * 2. FALLBACK PATH (always available): If Python is not installed, the
 *    script fails, or the call times out, every computation falls back
 *    to the pure-PHP implementation below. The /analytics page will
 *    NEVER show a blank state or an error because of a Python failure.
 *
 * Why Python for analytics (not recommendations)?
 * ───────────────────────────────────────────────
 * Recommendations run inline on every dashboard/page load → must be fast → PHP.
 * Analytics is a heavier, periodic computation → natural fit for pandas groupby.
 * The underlying method (simple moving average) is IDENTICAL in both paths;
 * only the implementation language changes. This is viva-defensible.
 *
 * TIME WINDOW
 * ───────────
 * ANALYTICS_LOOKBACK_DAYS = null → use ALL historical data.
 * Rationale: with <50 bookings, any rolling window (e.g. 90 days) would leave
 * too few data points for a meaningful trend line. In a production deployment
 * with sufficient booking volume, change this to e.g. 90 to reflect recent trends
 * rather than being diluted by old data.
 *
 * METHODOLOGY (for viva)
 * ───────────────────────
 * - Demand forecast  : Simple moving average — bookings per (facility, dow, hour)
 *                      ÷ number of distinct weeks in dataset.
 * - Revenue trend    : Weekly totals + linear extrapolation of mean Δ (no ML library).
 * - Utilisation      : booked_slots ÷ (days × 14 slots/day) × 100%.
 * - Under-booking    : (facility, dow, hour) where avg/week < UNDERUSE_THRESHOLD.
 */
class AnalyticsService
{
    // ─── Configuration constants ─────────────────────────────────────────────

    /**
     * Use null = all historical data.
     * Change to e.g. 90 in production when the dataset is large enough
     * that a rolling window improves forecast relevance.
     */
    private const ANALYTICS_LOOKBACK_DAYS = null;

    /** Seconds before the Python process is killed and PHP fallback kicks in. */
    private const PYTHON_TIMEOUT_SECONDS = 10;

    /** Slots per day (08:00–21:00, 14 one-hour slots). */
    private const SLOTS_PER_DAY = 14;

    /**
     * Under-booking threshold: avg bookings/week for a (facility, dow, hour)
     * combination. Below this value the slot is flagged as under-utilised.
     */
    private const UNDERUSE_THRESHOLD = 0.3;

    private const SLOT_HOURS = [
        '08:00','09:00','10:00','11:00','12:00',
        '13:00','14:00','15:00','16:00','17:00',
        '18:00','19:00','20:00','21:00',
    ];

    private const DOW_LABELS = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
        4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday',
    ];

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * Return the full analytics payload.
     *
     * @param  int|null $lookbackDays  null = all data (see ANALYTICS_LOOKBACK_DAYS)
     * @param  int      $projectionWeeks
     * @return array<string, mixed>
     */
    public function getReport(?int $lookbackDays = null, int $projectionWeeks = 4): array
    {
        // ── Try Python engine first ──────────────────────────────────────────
        $pythonResult = $this->tryPythonEngine($lookbackDays, $projectionWeeks);
        if ($pythonResult !== null) {
            return $pythonResult;
        }

        // ── PHP fallback ─────────────────────────────────────────────────────
        // Reaches here if: Python not installed, script error, or timeout.
        // The /analytics page is fully functional on this path alone.
        Log::info('AnalyticsService: using PHP fallback engine');

        $bookings = $this->loadBookings($lookbackDays);

        return [
            'demand_heatmap'   => $this->computeDemandHeatmap($bookings),
            'top_slots'        => $this->computeTopSlots($bookings),
            'revenue_trend'    => $this->computeRevenueTrend($lookbackDays, $projectionWeeks),
            'utilisation'      => $this->computeUtilisation($bookings, $lookbackDays),
            'under_booked'     => $this->computeUnderBooked($bookings, $lookbackDays),
            'summary_stats'    => $this->computeSummaryStats($bookings),
            'lookback_days'    => $lookbackDays,
            'projection_weeks' => $projectionWeeks,
            'engine'           => 'php',
        ];
    }

    // ─── Python engine ────────────────────────────────────────────────────────

    /**
     * Attempt to compute analytics via scripts/analytics_engine.py.
     * Returns null on any failure so the caller can use the PHP fallback.
     */
    private function tryPythonEngine(?int $lookbackDays, int $projectionWeeks): ?array
    {
        try {
            $input = $this->buildPythonInput($lookbackDays, $projectionWeeks);
            $json  = json_encode($input);

            // PYTHON_BIN env var lets you override 'python' → 'python3' on Linux/Mac.
            // Default is 'python' which works on Windows where Python 3 is registered as 'python'.
            $pythonBin = env('PYTHON_BIN', 'python');

            $result = Process::timeout(self::PYTHON_TIMEOUT_SECONDS)
                ->path(base_path('scripts'))
                ->input($json)
                ->run("{$pythonBin} analytics_engine.py");

            if (!$result->successful()) {
                Log::warning('AnalyticsService: Python engine exited with error', [
                    'exit_code' => $result->exitCode(),
                    'stderr'    => substr($result->errorOutput(), 0, 500),
                ]);
                return null;
            }

            $decoded = json_decode($result->output(), true);
            if (!is_array($decoded)) {
                Log::warning('AnalyticsService: Python engine returned invalid JSON');
                return null;
            }

            Log::info('AnalyticsService: Python engine succeeded');
            return $this->normalizePythonOutput($decoded, $lookbackDays, $projectionWeeks);

        } catch (\Throwable $e) {
            Log::warning('AnalyticsService: Python engine exception — using PHP fallback', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Build the JSON payload sent to analytics_engine.py via stdin.
     * Only serialises the fields the Python script needs — no PII.
     */
    private function buildPythonInput(?int $lookbackDays, int $projectionWeeks): array
    {
        $bookingsRaw = Booking::whereIn('status', ['confirmed', 'cancel_requested'])
            ->when($lookbackDays, fn($q) =>
                $q->where('booking_date', '>=', Carbon::now()->subDays($lookbackDays)->toDateString())
            )
            ->get(['facility_id', 'booking_date', 'slot_start', 'total_price'])
            ->map(fn($b) => [
                'facility_id'  => (int) $b->facility_id,
                'booking_date' => $b->booking_date->toDateString(),
                // Trim seconds: '08:00:00' → '08:00'
                'slot_start'   => substr($b->slot_start, 0, 5),
                'total_price'  => (float) $b->total_price,
            ])
            ->toArray();

        $facilitiesRaw = Facility::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($f) => ['id' => (int) $f->id, 'name' => $f->name])
            ->toArray();

        return [
            'bookings'           => array_values($bookingsRaw),
            'facilities'         => array_values($facilitiesRaw),
            'today'              => Carbon::today()->toDateString(),
            'lookback_days'      => $lookbackDays,
            'projection_weeks'   => $projectionWeeks,
            'underuse_threshold' => self::UNDERUSE_THRESHOLD,
            'slots_per_day'      => self::SLOTS_PER_DAY,
        ];
    }

    /**
     * Normalise the Python script's JSON output so it matches what Blade views expect.
     *
     * JSON object keys are always strings. The Blade view uses integer facility_id
     * and integer dow keys for the demand_heatmap — this method re-casts them.
     * Collections are also restored so Blade's ->isEmpty(), ->pluck() etc. work.
     */
    private function normalizePythonOutput(array $data, ?int $lookbackDays, int $projectionWeeks): array
    {
        // Restore integer keys in the nested heatmap array
        $rawHeatmap = $data['demand_heatmap'] ?? [];
        $heatmap = [];
        foreach ($rawHeatmap as $facilityId => $dows) {
            $heatmap[(int)$facilityId] = [];
            foreach ((array)$dows as $dow => $hours) {
                $heatmap[(int)$facilityId][(int)$dow] = (array)$hours;
            }
        }

        // top_slots and under_booked are lists → wrap as Collections for Blade
        $topSlots  = collect($data['top_slots']   ?? []);
        $underBook = collect($data['under_booked'] ?? []);

        // utilisation is a list of dicts → Collection so Blade can ->pluck()
        $utilisation = collect($data['utilisation'] ?? []);

        // revenue_trend is a plain associative array — no conversion needed
        $revenueTrend = $data['revenue_trend'] ?? [
            'labels' => [], 'actuals' => [], 'projected_labels' => [],
            'projected' => [], 'mean_delta' => 0.0,
        ];

        return [
            'demand_heatmap'   => $heatmap,
            'top_slots'        => $topSlots,
            'revenue_trend'    => $revenueTrend,
            'utilisation'      => $utilisation,
            'under_booked'     => $underBook,
            'summary_stats'    => $data['summary_stats'] ?? $this->emptySummaryStats(),
            'lookback_days'    => $lookbackDays,
            'projection_weeks' => $projectionWeeks,
            'engine'           => 'python',
        ];
    }

    // ─── PHP Fallback: data loading ───────────────────────────────────────────

    private function loadBookings(?int $lookbackDays): Collection
    {
        return Booking::whereIn('status', ['confirmed', 'cancel_requested'])
            ->when($lookbackDays, fn($q) =>
                $q->where('booking_date', '>=', Carbon::now()->subDays($lookbackDays)->toDateString())
            )
            ->get();
    }

    // ─── PHP Fallback: computation methods ────────────────────────────────────

    /**
     * Demand heatmap: average bookings per (facility, dow, hour).
     * Returns: [facility_id => [dow => [hour => avg_count]]]
     */
    private function computeDemandHeatmap(Collection $bookings): array
    {
        if ($bookings->isEmpty()) return [];

        $weeks = max(1, $bookings->unique(fn($b) => $b->booking_date->format('oW'))->count());

        $heatmap = [];
        foreach ($bookings->groupBy('facility_id') as $facilityId => $fBookings) {
            foreach ($fBookings->groupBy(fn($b) => $b->booking_date->dayOfWeek) as $dow => $dBookings) {
                foreach ($dBookings->groupBy(fn($b) => substr($b->slot_start, 0, 5)) as $hour => $hBookings) {
                    $heatmap[(int)$facilityId][(int)$dow][$hour] = round($hBookings->count() / $weeks, 2);
                }
            }
        }
        return $heatmap;
    }

    /**
     * Top 10 (facility, dow, hour) combinations by avg weekly demand.
     */
    private function computeTopSlots(Collection $bookings): Collection
    {
        if ($bookings->isEmpty()) return collect();

        $facilities = Facility::pluck('name', 'id');
        $weeks = max(1, $bookings->unique(fn($b) => $b->booking_date->format('oW'))->count());

        return $bookings
            ->groupBy(fn($b) =>
                $b->facility_id . '|' . $b->booking_date->dayOfWeek . '|' . substr($b->slot_start, 0, 5)
            )
            ->map(function ($group, $key) use ($facilities, $weeks) {
                [$facilityId, $dow, $hour] = explode('|', $key);
                return [
                    'facility_id'    => (int)$facilityId,
                    'facility_name'  => $facilities->get((int)$facilityId, 'Unknown'),
                    'dow'            => (int)$dow,
                    'dow_label'      => self::DOW_LABELS[(int)$dow] ?? 'Unknown',
                    'hour'           => $hour,
                    'avg_per_week'   => round($group->count() / $weeks, 2),
                    'total_bookings' => $group->count(),
                ];
            })
            ->sortByDesc('avg_per_week')
            ->take(10)
            ->values();
    }

    /**
     * Weekly revenue totals + linear extrapolation projection.
     *
     * PROJECTION: mean week-over-week Δ is computed from actuals,
     * then projected[k] = last_actual + k × Δ (clamped ≥ 0).
     * Same formula as analytics_engine.py — only language differs.
     */
    private function computeRevenueTrend(?int $lookbackDays, int $projectionWeeks): array
    {
        $query = Booking::whereIn('status', ['confirmed', 'cancel_requested'])
            ->selectRaw("DATE_FORMAT(booking_date, '%x-W%v') as iso_week, SUM(total_price) as revenue")
            ->groupBy('iso_week')
            ->orderBy('iso_week');

        if ($lookbackDays !== null) {
            $query->where('booking_date', '>=', Carbon::now()->subDays($lookbackDays)->toDateString());
        }

        $rows    = $query->get();
        $labels  = $rows->pluck('iso_week')->toArray();
        $actuals = $rows->pluck('revenue')->map(fn($v) => (float)$v)->toArray();

        $deltas    = [];
        for ($i = 1; $i < count($actuals); $i++) $deltas[] = $actuals[$i] - $actuals[$i - 1];
        $meanDelta = count($deltas) > 0 ? array_sum($deltas) / count($deltas) : 0;

        $lastActual      = end($actuals) ?: 0;
        $projectedLabels = [];
        $projected       = [];
        for ($k = 1; $k <= $projectionWeeks; $k++) {
            $projectedLabels[] = Carbon::now()->addWeeks($k)->format('o-\WW') . ' (proj.)';
            $projected[]       = max(0, round($lastActual + $k * $meanDelta, 2));
        }

        return [
            'labels'           => $labels,
            'actuals'          => $actuals,
            'projected_labels' => $projectedLabels,
            'projected'        => $projected,
            'mean_delta'       => round($meanDelta, 2),
        ];
    }

    /**
     * Utilisation rate per facility (booked ÷ total possible × 100%).
     */
    private function computeUtilisation(Collection $bookings, ?int $lookbackDays): Collection
    {
        $facilities = Facility::orderBy('name')->get();

        if ($bookings->isEmpty()) {
            return $facilities->map(fn($f) => [
                'facility_id'   => $f->id,
                'facility_name' => $f->name,
                'utilisation'   => 0.0,
                'booked_slots'  => 0,
                'total_slots'   => 0,
            ]);
        }

        if ($lookbackDays !== null) {
            $windowStart = Carbon::now()->subDays($lookbackDays);
        } else {
            $earliest    = $bookings->min(fn($b) => $b->booking_date->toDateString());
            $windowStart = Carbon::parse($earliest ?? now()->toDateString());
        }

        $windowDays = max(1, (int)$windowStart->diffInDays(Carbon::today()) + 1);
        $totalSlots = $windowDays * self::SLOTS_PER_DAY;
        $booked     = $bookings->groupBy('facility_id')->map->count();

        return $facilities->map(fn($f) => [
            'facility_id'   => $f->id,
            'facility_name' => $f->name,
            'utilisation'   => $totalSlots > 0
                ? round(($booked->get($f->id, 0) / $totalSlots) * 100, 1)
                : 0.0,
            'booked_slots'  => $booked->get($f->id, 0),
            'total_slots'   => $totalSlots,
        ]);
    }

    /**
     * Under-booked (facility, dow, hour) combinations below UNDERUSE_THRESHOLD.
     */
    private function computeUnderBooked(Collection $bookings, ?int $lookbackDays): Collection
    {
        if ($bookings->isEmpty()) return collect();

        $facilities = Facility::pluck('name', 'id');
        $weeks      = max(1, $bookings->unique(fn($b) => $b->booking_date->format('oW'))->count());

        $demandMap = $bookings
            ->groupBy(fn($b) =>
                $b->facility_id . '|' . $b->booking_date->dayOfWeek . '|' . substr($b->slot_start, 0, 5)
            )
            ->map(fn($g) => round($g->count() / $weeks, 2));

        $suggestions = collect();
        foreach ($facilities as $facilityId => $facilityName) {
            foreach (array_keys(self::DOW_LABELS) as $dow) {
                foreach (self::SLOT_HOURS as $hour) {
                    $avg = $demandMap->get("{$facilityId}|{$dow}|{$hour}", 0.0);
                    if ($avg < self::UNDERUSE_THRESHOLD) {
                        $period = $this->timePeriodLabel($hour);
                        $suggestions->push([
                            'facility_id'   => $facilityId,
                            'facility_name' => $facilityName,
                            'dow'           => $dow,
                            'dow_label'     => self::DOW_LABELS[$dow] ?? 'Unknown',
                            'hour'          => $hour,
                            'avg_per_week'  => $avg,
                            'suggestion'    => "{$facilityName} is under-booked on " .
                                (self::DOW_LABELS[$dow] ?? '') . " {$period} ({$hour}). " .
                                "Consider a promotion or price adjustment.",
                        ]);
                    }
                }
            }
        }

        return $suggestions->sortBy('avg_per_week')->take(15)->values();
    }

    private function computeSummaryStats(Collection $bookings): array
    {
        return [
            'total_bookings'    => $bookings->count(),
            'total_revenue'     => round($bookings->sum('total_price'), 2),
            'unique_facilities' => $bookings->pluck('facility_id')->unique()->count(),
            'date_range_start'  => $bookings->min(fn($b) => $b->booking_date->toDateString()),
            'date_range_end'    => $bookings->max(fn($b) => $b->booking_date->toDateString()),
        ];
    }

    private function emptySummaryStats(): array
    {
        return [
            'total_bookings'    => 0, 'total_revenue' => 0.0,
            'unique_facilities' => 0, 'date_range_start' => null, 'date_range_end' => null,
        ];
    }

    private function timePeriodLabel(string $hour): string
    {
        $h = (int)substr($hour, 0, 2);
        if ($h < 12) return 'mornings';
        if ($h < 17) return 'afternoons';
        return 'evenings';
    }
}
