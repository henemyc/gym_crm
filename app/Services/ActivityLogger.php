<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;

class ActivityLogger
{
    public function record(Lead $lead, ?User $user, string $event, string $title, ?string $description = null, array $metadata = []): LeadActivity
    {
        return $lead->activities()->create([
            'user_id' => $user?->id,
            'event' => $event,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata ?: null,
        ]);
    }
}
