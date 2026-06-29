<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    // ─── User: Show feedback form for a completed booking group ──────────────

    public function create(int $bookingGroupId): View|RedirectResponse
    {
        $user = Auth::user();

        // Only non-officer, non-admin users can submit
        if ($user->isAdmin() || $user->isRentalOfficer()) {
            abort(403);
        }

        // Load the representative (leader) booking for this group
        $booking = Booking::with(['facility', 'user'])
            ->where(function($query) use ($bookingGroupId) {
                $query->where('booking_group_id', $bookingGroupId)
                      ->orWhere(function($q) use ($bookingGroupId) {
                          $q->whereNull('booking_group_id')
                            ->where('id', $bookingGroupId);
                      });
            })
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Ensure all bookings in the group are confirmed
        $groupBookings = Booking::where(function($query) use ($booking) {
            if ($booking->booking_group_id) {
                $query->where('booking_group_id', $booking->booking_group_id);
            } else {
                $query->where('id', $booking->id);
            }
        })
        ->orderBy('slot_start')
        ->get();

        $allConfirmed = $groupBookings->every(fn($b) => $b->status === 'confirmed');
        if (! $allConfirmed) {
            return redirect()->route('bookings.my')
                ->with('error', 'This booking is not yet confirmed.');
        }

        // Ensure the last slot has ended (facility has been used)
        $lastBooking = $groupBookings->last();
        if (! $lastBooking->isCompleted()) {
            return redirect()->route('bookings.my')
                ->with('error', 'You can only leave feedback after the booking slot has ended.');
        }

        // Ensure no feedback has been submitted yet
        if (Feedback::where('booking_group_id', $bookingGroupId)->exists()) {
            return redirect()->route('feedback.my')
                ->with('info', 'You have already submitted feedback for this booking.');
        }

        return view('feedback.create', compact('booking', 'groupBookings'));
    }

    // ─── User: Store submitted feedback ──────────────────────────────────────

    public function store(Request $request, int $bookingGroupId): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isRentalOfficer()) {
            abort(403);
        }

        // Verify ownership
        $booking = Booking::where(function($query) use ($bookingGroupId) {
            $query->where('booking_group_id', $bookingGroupId)
                  ->orWhere(function($q) use ($bookingGroupId) {
                      $q->whereNull('booking_group_id')
                        ->where('id', $bookingGroupId);
                  });
        })
        ->where('user_id', $user->id)
        ->firstOrFail();

        // Prevent duplicate feedback
        if (Feedback::where('booking_group_id', $bookingGroupId)->exists()) {
            return redirect()->route('feedback.my')
                ->with('info', 'You have already submitted feedback for this booking.');
        }

        // Verify the last slot has ended
        $lastBooking = Booking::where(function($query) use ($booking) {
            if ($booking->booking_group_id) {
                $query->where('booking_group_id', $booking->booking_group_id);
            } else {
                $query->where('id', $booking->id);
            }
        })
        ->orderByDesc('slot_start')
        ->first();

        if (! $lastBooking || ! $lastBooking->isCompleted()) {
            return redirect()->route('bookings.my')
                ->with('error', 'Feedback cannot be submitted before the booking slot ends.');
        }

        $data = $request->validate([
            'rating'        => ['required', 'integer', 'min:0', 'max:5'],
            'title'         => ['required', 'string', 'max:255'],
            'message'       => ['required', 'string', 'max:2000'],
        ]);

        Feedback::create([
            'booking_group_id' => $bookingGroupId,
            'user_id'          => $user->id,
            'facility_id'      => $booking->facility_id,
            'rating'           => $data['rating'],
            'title'            => $data['title'],
            'message'          => $data['message'],
            'feedback_time'    => now(),
        ]);

        return redirect()->route('feedback.my')
            ->with('success', 'Thank you for your feedback!');
    }

    // ─── User: View own feedbacks ─────────────────────────────────────────────

    public function myFeedbacks(Request $request): View
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isRentalOfficer()) {
            abort(403);
        }

        $search = trim($request->query('search', ''));
        $rating = $request->query('rating', '');

        $query = Feedback::with(['facility', 'booking'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        if ($search !== '') {
            $query->whereHas('facility', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($rating !== null && $rating !== '') {
            $query->where('rating', $rating);
        }

        $feedbacks = $query->paginate(15)->appends($request->query());

        return view('feedback.my', compact('feedbacks', 'search', 'rating'));
    }

    // ─── Rental Officer: View all feedbacks ───────────────────────────────────

    public function index(Request $request): View
    {
        $facilityId = $request->query('facility');
        $rating     = $request->query('rating');
        $search     = trim($request->query('search', ''));

        $query = Feedback::with(['user', 'facility', 'booking'])
            ->orderByDesc('created_at');

        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        if ($rating !== null && $rating !== '') {
            $query->where('rating', $rating);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('fullname', 'like', "%{$search}%"))
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $feedbacks  = $query->paginate(20)->appends($request->query());
        $facilities = Facility::orderBy('name')->get();

        return view('feedback.all', compact('feedbacks', 'facilities', 'facilityId', 'rating', 'search'));
    }
}
