<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_group_id',
        'stripe_session_id',
        'transaction_id',
        'payment_status',
        'amount',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    /**
     * Returns the group-leader booking (first slot in the session).
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_group_id', 'booking_group_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function statusLabel(): string
    {
        return match ($this->payment_status) {
            'pending'   => 'Pending',
            'completed' => 'Paid',
            'failed'    => 'Failed',
            default     => ucfirst($this->payment_status),
        };
    }
}
