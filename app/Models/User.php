<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = [
        'admin' => 'Admin',
        'manager' => 'Manager',
        'sales' => 'Sales rep',
    ];

    public const ONLINE_WINDOW_MINUTES = 5;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'phone', 'timezone', 'notifications_enabled',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'notifications_enabled' => 'boolean',
        ];
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function createdLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    public function assignedFollowUps(): HasMany
    {
        return $this->hasMany(FollowUp::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function imports(): HasMany
    {
        return $this->hasMany(LeadImport::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canManageTeam(): bool
    {
        return in_array($this->role, ['admin', 'manager'], true);
    }

    public function isOnlineNow(): bool
    {
        return $this->last_activity_at !== null
            && $this->last_activity_at->greaterThanOrEqualTo(now()->subMinutes(self::ONLINE_WINDOW_MINUTES));
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)) ?: [])
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
