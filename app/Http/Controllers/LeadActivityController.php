<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadActivityController extends Controller
{
    public function store(Request $request, Lead $lead, ActivityLogger $activityLogger): RedirectResponse|JsonResponse
    {
        $this->authorize('view', $lead);
        $data = $request->validate([
            'event' => ['required', Rule::in(['note', 'call', 'whatsapp'])],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
        $titles = ['note' => 'Note added', 'call' => 'Call logged', 'whatsapp' => 'WhatsApp message opened'];
        $activityLogger->record($lead, $request->user(), $data['event'], $titles[$data['event']], $data['description'] ?? null);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $titles[$data['event']]]);
        }

        return back()->with('success', 'Activity added to the timeline.');
    }
}
