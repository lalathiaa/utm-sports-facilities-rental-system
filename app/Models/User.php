<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'username',
        'ic_number',
        'email',
        'password',
        'role',
        'status',
        'matric_number',
        'staff_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    // ─── Role Helper Methods ────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRentalOfficer(): bool
    {
        return $this->role === 'rental_officer';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isGuest(): bool
    {
        return $this->role === 'guest';
    }

    /**
     * Derive the original role from the user's email domain.
     */
    public function deriveRoleFromEmail(): string
    {
        if (str_ends_with($this->email, '@graduate.utm.my')) {
            return 'student';
        }
        if (str_ends_with($this->email, '@utm.my')) {
            return 'staff';
        }
        return 'guest';
    }

    /**
     * Human-readable role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'          => 'Administrator',
            'rental_officer' => 'Rental Officer',
            'staff'          => 'UTM Staff',
            'student'        => 'UTM Student',
            'guest'          => 'Guest',
            default          => ucfirst($this->role),
        };
    }
}
