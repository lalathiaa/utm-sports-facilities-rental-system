<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_group_id',
        'user_id',
        'facility_id',
        'booking_date',
        'slot_start',
        'slot_end',
        'status',
        'total_price',
        'cancellation_reason',
        'payment_expires_at',
    ];

    protected $casts = [
        'booking_date'       => 'date',
        'payment_expires_at' => 'datetime',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'booking_equipment')
                    ->withPivot('price_snapshot', 'quantity')
                    ->withTimestamps();
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class);
    }

    /**
     * Feedback submitted for this booking group (keyed on the group leader booking).
     */
    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'booking_group_id', 'booking_group_id');
    }

    /**
     * Payment is linked via booking_group_id — only the group leader has it.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'booking_group_id', 'booking_group_id');
    }

    /**
     * All bookings that belong to the same payment session (same group).
     */
    public function groupedBookings()
    {
        return Booking::where('booking_group_id', $this->booking_group_id)
            ->orderBy('slot_start')
            ->get();
    }

    public function primaryParticipant()
    {
        return $this->participants()->where('is_primary', true)->first();
    }

    public static function expireStaleBookings(): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $expiredGroups = self::select('booking_group_id')
                ->where('status', 'pending_payment')
                ->where('payment_expires_at', '<', now())
                ->whereNotNull('booking_group_id')
                ->distinct()
                ->pluck('booking_group_id');

            // 1. Update all expired pending bookings to failed (even if booking_group_id is null)
            self::where('status', 'pending_payment')
                ->where('payment_expires_at', '<', now())
                ->update(['status' => 'failed']);

            // 2. Update matching payments to failed
            if ($expiredGroups->isNotEmpty()) {
                Payment::whereIn('booking_group_id', $expiredGroups)
                    ->where('payment_status', 'pending')
                    ->update(['payment_status' => 'failed']);
            }
        });
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    public function slotLabel(): string
    {
        $start = substr($this->slot_start, 0, 5);
        $end   = substr($this->slot_end, 0, 5);
        return "{$start} – {$end}";
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelRequested(): bool
    {
        return $this->status === 'cancel_requested';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPendingPayment(): bool
    {
        return $this->status === 'pending_payment';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * A booking slot is "completed" when it is confirmed and its end time is in the past.
     * Used to determine feedback eligibility.
     */
    public function isCompleted(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }
        $slotEnd = Carbon::parse($this->booking_date->toDateString() . ' ' . $this->slot_end);
        return $slotEnd->isPast();
    }

    public function isPaymentExpired(): bool
    {
        return $this->isPendingPayment()
            && $this->payment_expires_at
            && $this->payment_expires_at->isPast();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'confirmed'        => 'Confirmed',
            'cancel_requested' => 'Cancellation Requested',
            'cancelled'        => 'Cancelled',
            'pending_payment'  => 'Pending Payment',
            'failed'           => 'Payment Failed',
            default            => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'confirmed'        => 'bg-green-100 text-green-800',
            'cancel_requested' => 'bg-yellow-100 text-yellow-800',
            'cancelled'        => 'bg-red-100 text-red-800',
            'pending_payment'  => 'bg-blue-100 text-blue-800',
            'failed'           => 'bg-red-100 text-red-800',
            default            => 'bg-gray-100 text-gray-600',
        };
    }
}