<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityClosure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityClosureController extends Controller
{
    private const SLOT_HOURS = [
        '08:00','09:00','10:00','11:00','12:00',
        '13:00','14:00','15:00','16:00','17:00',
        '18:00','19:00','20:00','21:00',
    ];

    // ─── Show closure management page ───────────────────────────────────────

    public function index(Facility $facility): View
    {
        $facility->load('closures');

        $date = request('date', now()->toDateString());
        if ($date < now()->toDateString()) {
            $date = now()->toDateString();
        }

        // Closures for the selected date
        $closuresOnDate = $facility->closures()
            ->where('closure_date', $date)
            ->get();

        $hasFullDayClosure = $closuresOnDate->whereNull('slot_start')->isNotEmpty();

        $closedSlots = $hasFullDayClosure
            ? self::SLOT_HOURS
            : $closuresOnDate->pluck('slot_start')
                ->map(fn($t) => substr($t, 0, 5))
                ->toArray();

        // Upcoming closures (today onwards) grouped by date for the sidebar
        $upcomingClosures = $facility->closures()
            ->where('closure_date', '>=', now()->toDateString())
            ->orderBy('closure_date')
            ->orderBy('slot_start')
            ->get()
            ->groupBy(fn($c) => $c->closure_date->toDateString());

        return view('facilities.closures', compact(
            'facility',
            'date',
            'closuresOnDate',
            'hasFullDayClosure',
            'closedSlots',
            'upcomingClosures'
        ));
    }

    // ─── Add closure(s) ─────────────────────────────────────────────────────

    public function store(Request $request, Facility $facility): RedirectResponse
    {
        $data = $request->validate([
            'closure_date' => ['required', 'date', 'after_or_equal:today'],
            'closure_type' => ['required', 'in:full_day,specific_slots'],
            'slots'        => ['required_if:closure_type,specific_slots', 'array', 'min:1'],
            'slots.*'      => ['required', 'in:' . implode(',', self::SLOT_HOURS)],
            'reason'       => ['nullable', 'string', 'max:255'],
        ]);

        $date   = $data['closure_date'];
        $reason = $data['reason'] ?? null;

        if ($data['closure_type'] === 'full_day') {
            // Remove any existing slot-specific closures for this date first
            $facility->closures()->where('closure_date', $date)->delete();

            // Insert a single full-day closure (slot_start = null)
            FacilityClosure::create([
                'facility_id'  => $facility->id,
                'closure_date' => $date,
                'slot_start'   => null,
                'reason'       => $reason,
            ]);

            return back()->with('success', "Full-day closure set for {$date}.");
        }

        // Specific slots — skip if already closed or full-day closure exists
        if ($facility->closures()->where('closure_date', $date)->whereNull('slot_start')->exists()) {
            return back()->withErrors(['slots' => 'This date already has a full-day closure.'])->withInput();
        }

        $added = 0;
        foreach ($data['slots'] as $slot) {
            FacilityClosure::firstOrCreate([
                'facility_id'  => $facility->id,
                'closure_date' => $date,
                'slot_start'   => $slot . ':00',
            ], [
                'reason' => $reason,
            ]);
            $added++;
        }

        return back()->with('success', "{$added} slot(s) marked as unavailable on {$date}.");
    }

    // ─── Remove a single closure ─────────────────────────────────────────────

    public function destroy(Facility $facility, FacilityClosure $closure): RedirectResponse
    {
        abort_if($closure->facility_id !== $facility->id, 403);
        $closure->delete();

        return back()->with('success', 'Closure removed successfully.');
    }

    // ─── Remove all closures for a date ──────────────────────────────────────

    public function destroyDate(Request $request, Facility $facility): RedirectResponse
    {
        $data = $request->validate([
            'closure_date' => ['required', 'date'],
        ]);

        $deleted = $facility->closures()
            ->where('closure_date', $data['closure_date'])
            ->delete();

        return back()->with('success', "All closures for {$data['closure_date']} have been removed.");
    }
}