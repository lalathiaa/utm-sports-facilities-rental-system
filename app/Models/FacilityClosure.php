<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'closure_date',
        'slot_start',
        'reason',
    ];

    protected $casts = [
        'closure_date' => 'date',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    public function isFullDay(): bool
    {
        return is_null($this->slot_start);
    }

    public function slotLabel(): string
    {
        if ($this->isFullDay()) {
            return 'Full Day';
        }
        $start = substr($this->slot_start, 0, 5);
        $end   = date('H:i', strtotime($this->slot_start) + 3600);
        return "{$start} – {$end}";
    }
}