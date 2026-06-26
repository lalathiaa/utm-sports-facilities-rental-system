<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'booking_group_id',
        'user_id',
        'facility_id',
        'rating',
        'title',
        'message',
        'feedback_time',
    ];

    protected $casts = [
        'rating'        => 'integer',
        'feedback_time' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * The representative (leader) booking for this feedback's group.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_group_id', 'id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Star string for display (e.g. "★★★★☆" for rating 4).
     */
    public function starDisplay(): string
    {
        $filled = str_repeat('★', $this->rating);
        $empty  = str_repeat('☆', 5 - $this->rating);
        return $filled . $empty;
    }
}
