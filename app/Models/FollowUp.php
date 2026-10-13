<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
{
    use SoftDeletes;

    public const KINDS = ['follow_up' => 'Follow-up', 'meeting' => 'Meeting', 'trial' => 'Trial'];

    public const OUTCOMES = [
        'interested' => 'Interested',
        'asked_to_call_back' => 'Asked to call back',
        'not_reachable' => 'Not reachable',
        'meeting_fixed' => 'Meeting fixed',
        'trial' => 'Trial',
        'not_interested' => 'Not interested',
    ];

    protected $fillable = [
        'lead_id', 'assigned_to', 'created_by', 'kind', 'due_at', 'outcome', 'remark', 'completed_at',
        'next_follow_up_at', 'meeting_at', 'meeting_with', 'trial_at', 'trial_details',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'meeting_at' => 'datetime',
            'trial_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->canManageTeam()
            ? $query
            : $query->where('assigned_to', $user->id)
                ->whereHas('lead', fn (Builder $leads) => $leads->where('assigned_to', $user->id));
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('completed_at');
    }
}
