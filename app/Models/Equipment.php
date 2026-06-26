<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'facility_id',
        'name',
        'price',
        'status',
        'image',
        'quantity',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_equipment')
                    ->withPivot('price_snapshot', 'quantity')
                    ->withTimestamps();
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->quantity > 0;
    }
}