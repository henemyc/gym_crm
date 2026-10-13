<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GooglePlaceProspect extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'not_interested' => 'Not interested',
        'converted' => 'Converted to lead',
    ];

    protected $fillable = ['place_id', 'assigned_to', 'created_by', 'lead_id', 'status'];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(GooglePlaceProspectActivity::class, 'prospect_id')->latest();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->canManageTeam()
            ? $query
            : $query->where('assigned_to', $user->id);
    }

    public function isConverted(): bool
    {
        return $this->lead_id !== null || $this->status === 'converted';
    }
}
