<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Equipment;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    private const SLOT_HOURS = [
        '08:00', '09:00', '10:00', '11:00', '12:00',
        '13:00', '14:00', '15:00', '16:00', '17:00',
        '18:00', '19:00', '20:00', '21:00',
    ];

    // ─── Show booking form ───────────────────────────────────────────────────

    public function create(Facility $facility): View
    {
        abort_if(!$facility->isAvailable(), 403, 'This facility is currently not available for booking.');

        $facility->load('equipment');

        $date = request('date', now()->toDateString());
        if ($date < now()->toDateString()) {
            $date = now()->toDateString();
        }

        $bookedSlots = $facility->unavailableSlotsOn($date);
        $user        = Auth::user();

        return view('bookings.create', compact('facility', 'date', 'bookedSlots', 'user'));
    }

    // ─── Store a new booking ─────────────────────────────────────────────────

    public function store(Request $request, Facility $facility): RedirectResponse
    {
        abort_if(!$facility->isAvailable(), 403, 'This facility is currently not available for booking.');

        $additionalCount = $facility->additionalParticipantsRequired();

        // Build dynamic validation rules
        $rules = [
            'booking_date'              => ['required', 'date', 'after_or_equal:today'],
            'slots'                     => ['required', 'array', 'min:1'],
            'slots.*'                   => ['required', 'in:' . implode(',', self::SLOT_HOURS)],

            // Primary participant (renter)
            'primary_fullname'          => ['required', 'string', 'max:255'],
            'primary_ic_number'         => ['required', 'string', 'max:20'],
            'primary_matric_number'     => ['nullable', 'string', 'max:20'],

            // Equipment (optional)
            'equipment'                 => ['nullable', 'array'],
            'equipment.*.id'            => ['required', 'integer', 'exists:equipment,id'],
            'equipment.*.quantity'      => ['required', 'integer', 'min:1'],
        ];

        // Additional participants
        for ($i = 0; $i < $additionalCount; $i++) {
            $rules["participants.{$i}.fullname"]      = ['required', 'string', 'max:255'];
            $rules["participants.{$i}.ic_number"]     = ['required', 'string', 'max:20'];
            $rules["participants.{$i}.matric_number"] = ['nullable', 'string', 'max:20'];
        }

        $data = $request->validate($rules);

        $date          = $data['booking_date'];
        $selectedSlots = $data['slots'];

        // Validate and collect equipment
        $equipmentItems = collect();
        if (!empty($data['equipment'])) {
            foreach ($data['equipment'] as $eqData) {
                $eq = Equipment::where('id', $eqData['id'])
                    ->where('facility_id', $facility->id)
                    ->where('status', 'available')
                    ->first();

                if (!$eq) {
                    return back()->withErrors(['equipment' => 'Some selected equipment is unavailable.'])->withInput();
                }
                if ($eqData['quantity'] > $eq->quantity) {
                    return back()->withErrors(['equipment' => "Requested quantity for \"{$eq->name}\" exceeds available stock ({$eq->quantity})."])->withInput();
                }
                $equipmentItems->push(['model' => $eq, 'quantity' => (int) $eqData['quantity']]);
            }
        }

        // Check if any selected slot is closed by rental officer
        $closedSlots = $facility->closedSlotsOn($date);
        $closedConflicts = array_intersect($selectedSlots, $closedSlots);
        if (!empty($closedConflicts)) {
            return back()
                ->withErrors(['slots' => 'One or more selected slots are marked as unavailable by the facility manager.'])
                ->withInput();
        }

        // Check for already-booked slots
        $alreadyBooked = Booking::where('facility_id', $facility->id)
            ->where('booking_date', $date)
            ->whereIn('status', ['confirmed', 'cancel_requested', 'pending_payment'])
            ->whereIn('slot_start', array_map(fn($s) => $s . ':00', $selectedSlots))
            ->exists();

        if ($alreadyBooked) {
            return back()
                ->withErrors(['slots' => 'One or more selected slots has just been booked by someone else. Please choose different slots.'])
                ->withInput();
        }

        // Create bookings inside a transaction (1 row per slot)
        $firstBooking = null;
        $allBookings  = [];

        DB::transaction(function () use (
            $facility, $date, $selectedSlots, $equipmentItems,
            $data, $additionalCount, &$firstBooking, &$allBookings
        ) {
            $expiresAt = now()->addMinutes(10);

            foreach ($selectedSlots as $slotStart) {
                $slotEnd = date('H:i', strtotime($slotStart) + 3600);

                // Calculate total: facility price + sum(equipment price × qty)
                $eqTotal = $equipmentItems->sum(fn($e) => $e['model']->price * $e['quantity']);
                $total   = $facility->price + $eqTotal;

                $booking = Booking::create([
                    'user_id'            => Auth::id(),
                    'facility_id'        => $facility->id,
                    'booking_date'       => $date,
                    'slot_start'         => $slotStart . ':00',
                    'slot_end'           => $slotEnd . ':00',
                    'status'             => 'pending_payment',
                    'total_price'        => $total,
                    'payment_expires_at' => $expiresAt,
                ]);

                if ($firstBooking === null) $firstBooking = $booking;
                $allBookings[] = $booking;

                // Attach equipment
                foreach ($equipmentItems as $eqEntry) {
                    $booking->equipment()->attach($eqEntry['model']->id, [
                        'price_snapshot' => $eqEntry['model']->price,
                        'quantity'       => $eqEntry['quantity'],
                    ]);
                }

                // Primary participant
                BookingParticipant::create([
                    'booking_id'    => $booking->id,
                    'is_primary'    => true,
                    'fullname'      => $data['primary_fullname'],
                    'ic_number'     => $data['primary_ic_number'],
                    'matric_number' => $data['primary_matric_number'] ?? null,
                ]);

                // Additional participants
                for ($i = 0; $i < $additionalCount; $i++) {
                    BookingParticipant::create([
                        'booking_id'    => $booking->id,
                        'is_primary'    => false,
                        'fullname'      => $data['participants'][$i]['fullname'],
                        'ic_number'     => $data['participants'][$i]['ic_number'],
                        'matric_number' => $data['participants'][$i]['matric_number'] ?? null,
                    ]);
                }
            }

            // Set booking_group_id = first booking's ID for all slots in this session
            $groupId = $firstBooking->id;
            foreach ($allBookings as $b) {
                $b->update(['booking_group_id' => $groupId]);
            }
        });

        return redirect()->route('payment.prepare', $firstBooking->id)
                         ->with('info', 'Booking reserved! Please complete your payment within 10 minutes.');

    }

    // ─── Booking slip (receipt) ──────────────────────────────────────────────

    public function slip(Booking $booking): View
    {
        // Allow: owner, rental officer, admin
        $user = Auth::user();
        if (!$user->isRentalOfficer() && !$user->isAdmin() && $booking->user_id !== $user->id) {
            abort(403);
        }

        $booking->load(['facility', 'equipment', 'participants', 'user']);

        // Load all slots for the same booking session (same user, facility, date, created together)
        $relatedBookings = Booking::with(['equipment', 'participants'])
            ->where('user_id', $booking->user_id)
            ->where('facility_id', $booking->facility_id)
            ->where('booking_date', $booking->booking_date)
            ->where('status', $booking->status)
            ->orderBy('slot_start')
            ->get();

        return view('bookings.slip', compact('booking', 'relatedBookings'));
    }

    // ─── My Bookings (user) ──────────────────────────────────────────────────

    public function myBookings(Request $request): View
    {
        $search = trim($request->query('search', ''));
        $status = $request->query('status', 'all');

        $query = Booking::with(['facility', 'equipment', 'participants', 'feedback', 'payment'])
            ->where('user_id', Auth::id())
            ->orderByDesc('booking_date')
            ->orderByDesc('slot_start');

        if ($search !== '') {
            $query->whereHas('facility', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($status !== 'all' && $status !== '') {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15)->appends($request->query());

        return view('bookings.my', compact('bookings', 'search', 'status'));
    }

    // ─── User: Request cancellation ─────────────────────────────────────────

    public function requestCancel(Request $request, Booking $booking): RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(!$booking->isConfirmed(), 400, 'This booking cannot be cancelled.');

        $slotDateTime = $booking->booking_date->toDateString() . ' ' . $booking->slot_start;
        if (strtotime($slotDateTime) <= time()) {
            return back()->with('error', 'You cannot request cancellation for a past or ongoing booking.');
        }

        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $booking->update([
            'status'               => 'cancel_requested',
            'cancellation_reason'  => $data['cancellation_reason'],
        ]);

        return back()->with('success', 'Cancellation request submitted. Please wait for Rental Officer approval.');
    }

    // ─── Rental Officer: View all bookings ───────────────────────────────────

    public function allBookings(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = trim($request->query('search', ''));
        $date   = $request->query('date', '');

        $query = Booking::with(['facility', 'user', 'participants'])
            ->orderByDesc('booking_date')
            ->orderByDesc('slot_start');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('fullname', 'like', "%{$search}%")
                                                  ->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('facility', fn($f) => $f->where('name', 'like', "%{$search}%"));
            });
        }

        if ($date !== '') {
            $query->whereDate('booking_date', $date);
        }

        $bookings = $query->paginate(20)->appends($request->query());

        return view('bookings.all', compact('bookings', 'status', 'search', 'date'));
    }

    // ─── Rental Officer: Approve cancellation ────────────────────────────────

    public function approveCancel(Booking $booking): RedirectResponse
    {
        abort_if(!$booking->isCancelRequested(), 400, 'No cancellation request found.');

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', "Booking #{$booking->id} cancellation approved.");
    }

    // ─── Rental Officer: Reject cancellation request ─────────────────────────

    public function rejectCancel(Booking $booking): RedirectResponse
    {
        abort_if(!$booking->isCancelRequested(), 400, 'No cancellation request found.');

        $booking->update([
            'status'              => 'confirmed',
            'cancellation_reason' => null,
        ]);

        return back()->with('success', "Booking #{$booking->id} cancellation request rejected. Booking restored.");
    }

    // ─── Rental Officer: Directly cancel a booking ───────────────────────────

    public function cancelByOfficer(Booking $booking): RedirectResponse
    {
        abort_if($booking->isCancelled(), 400, 'Booking is already cancelled.');

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', "Booking #{$booking->id} has been cancelled.");
    }

    // ─── AJAX: Available slots for a facility on a date ─────────────────────

    public function availableSlots(Facility $facility): \Illuminate\Http\JsonResponse
    {
        $date        = request('date', now()->toDateString());
        $bookedSlots = $facility->unavailableSlotsOn($date);

        $slots = array_map(function ($hour) use ($bookedSlots) {
            $end = date('H:i', strtotime($hour) + 3600);
            return [
                'start'     => $hour,
                'end'       => $end,
                'label'     => $hour . ' – ' . $end,
                'available' => !in_array($hour, $bookedSlots),
            ];
        }, self::SLOT_HOURS);

        return response()->json($slots);
    }
}