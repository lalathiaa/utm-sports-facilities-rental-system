<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'status',
        'image',
        'required_participants',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function closures(): HasMany
    {
        return $this->hasMany(FacilityClosure::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Get booked slot_start times for a given date (bookings only).
     * Treats confirmed + cancel_requested as occupied.
     * Pending-payment slots are only treated as occupied while their payment
     * window is still open — expired reservations are transparent to other users.
     */
    public function bookedSlotsOn(string $date): array
    {
        return $this->bookings()
            ->where('booking_date', $date)
            ->where(function ($query) {
                $query->whereIn('status', ['confirmed', 'cancel_requested'])
                      ->orWhere(function ($q) {
                          // Include pending_payment ONLY if its timer has not expired yet
                          $q->where('status', 'pending_payment')
                            ->where(function ($inner) {
                                $inner->whereNull('payment_expires_at')
                                      ->orWhere('payment_expires_at', '>', now());
                            });
                      });
            })
            ->pluck('slot_start')
            ->map(fn($t) => substr($t, 0, 5))
            ->toArray();
    }

    /**
     * Get closed slot_start times for a given date.
     * Returns ['08:00','09:00',...] for specific slots,
     * or ALL slots if a full-day closure exists.
     */
    public function closedSlotsOn(string $date): array
    {
        $allSlots = [
            '08:00','09:00','10:00','11:00','12:00',
            '13:00','14:00','15:00','16:00','17:00',
            '18:00','19:00','20:00','21:00',
        ];

        $closures = $this->closures()
            ->where('closure_date', $date)
            ->get();

        // If any full-day closure exists, all slots are closed
        if ($closures->whereNull('slot_start')->isNotEmpty()) {
            return $allSlots;
        }

        return $closures
            ->pluck('slot_start')
            ->map(fn($t) => substr($t, 0, 5))
            ->toArray();
    }

    /**
     * Combined unavailable slots: booked + closed.
     */
    public function unavailableSlotsOn(string $date): array
    {
        return array_unique(array_merge(
            $this->bookedSlotsOn($date),
            $this->closedSlotsOn($date)
        ));
    }

    /**
     * Number of additional participants needed (excluding the primary renter).
     */
    public function additionalParticipantsRequired(): int
    {
        return max(0, $this->required_participants - 1);
    }

    /**
     * Average star rating from submitted feedbacks (0.0 if none).
     */
    public function averageRating(): float
    {
        return round((float) ($this->feedbacks->avg('rating') ?? 0), 1);
    }

    /**
     * Total number of feedbacks for this facility.
     */
    public function feedbackCount(): int
    {
        return $this->feedbacks->count();
    }
}