<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GooglePlaceProspectActivity extends Model
{
    protected $fillable = [
        'prospect_id', 'actor_id', 'event', 'from_assigned_to', 'to_assigned_to', 'from_status', 'to_status',
    ];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(GooglePlaceProspect::class, 'prospect_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
