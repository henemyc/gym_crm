<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    public const PRIORITIES = ['hot' => 'Hot', 'warm' => 'Warm', 'cold' => 'Cold'];

    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'follow_up' => 'Follow-up',
        'meeting_fixed' => 'Meeting fixed',
        'trial' => 'Trial',
        'negotiation' => 'Negotiation',
        'converted' => 'Converted',
        'lost' => 'Lost',
    ];

    public const SOURCES = ['Website', 'Walk-in', 'Referral', 'Instagram', 'Facebook', 'Google', 'Google Maps', 'Phone', 'Campaign', 'Other'];

    protected $fillable = [
        'name', 'gym_name', 'phone', 'location', 'source', 'priority', 'status', 'notes', 'assigned_to', 'created_by',
    ];

    protected function casts(): array
    {
        return ['deleted_at' => 'datetime'];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class)->latest('due_at');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->canManageTeam()
            ? $query
            : $query->where('assigned_to', $user->id);
    }

    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $query, string $term): void {
                $term = trim($term);
                $query->where(function (Builder $search) use ($term): void {
                    $search->where('name', 'like', "%{$term}%")
                        ->orWhere('gym_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('location', 'like', "%{$term}%");
                });
            })
            ->when($filters['source'] ?? null, fn (Builder $q, string $value) => $q->where('source', $value))
            ->when($filters['location'] ?? null, fn (Builder $q, string $value) => $q->where('location', $value))
            ->when($filters['priority'] ?? null, fn (Builder $q, string $value) => $q->where('priority', $value))
            ->when($filters['status'] ?? null, fn (Builder $q, string $value) => $q->where('status', $value))
            ->when($filters['assigned_to'] ?? null, fn (Builder $q, string $value) => $q->where('assigned_to', $value))
            ->when($filters['from'] ?? null, fn (Builder $q, string $value) => $q->whereDate('created_at', '>=', $value))
            ->when($filters['to'] ?? null, fn (Builder $q, string $value) => $q->whereDate('created_at', '<=', $value));
    }

    public function getShortNameAttribute(): string
    {
        return str($this->name)->before(' ')->toString();
    }
}
