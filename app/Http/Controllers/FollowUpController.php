<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFollowUpRequest;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $view = $request->validate(['view' => ['nullable', Rule::in(['today', 'upcoming', 'overdue', 'completed', 'all'])], 'q' => ['nullable', 'string', 'max:100']]);
        $activeView = $view['view'] ?? 'today';
        $today = now($user->timezone ?: config('app.timezone'));
        $base = FollowUp::query()->visibleTo($user);
        $counts = [
            'today' => (clone $base)->open()->whereDate('due_at', $today->toDateString())->count(),
            'upcoming' => (clone $base)->open()->where('due_at', '>', $today->copy()->endOfDay())->count(),
            'overdue' => (clone $base)->open()->where('due_at', '<', $today->copy()->startOfDay())->count(),
            'completed' => (clone $base)->whereNotNull('completed_at')->count(),
            'all' => (clone $base)->count(),
        ];

        $query = (clone $base)->with(['lead:id,name,gym_name,phone,location,priority,status', 'assignee:id,name', 'creator:id,name']);
        $query->when($activeView === 'today', fn (Builder $q) => $q->open()->whereDate('due_at', $today->toDateString()))
            ->when($activeView === 'upcoming', fn (Builder $q) => $q->open()->where('due_at', '>', $today->copy()->endOfDay()))
            ->when($activeView === 'overdue', fn (Builder $q) => $q->open()->where('due_at', '<', $today->copy()->startOfDay()))
            ->when($activeView === 'completed', fn (Builder $q) => $q->whereNotNull('completed_at'));
        if (filled($view['q'] ?? null)) {
            $term = trim($view['q']);
            $query->whereHas('lead', fn (Builder $q) => $q->where('name', 'like', "%{$term}%")->orWhere('gym_name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%"));
        }
        $followUps = $query->orderByRaw('CASE WHEN completed_at IS NULL THEN 0 ELSE 1 END')->orderBy('due_at')->paginate(20)->withQueryString();

        return view('follow-ups.index', compact('followUps', 'counts', 'activeView', 'today'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $leadQuery = Lead::query()->visibleTo($user)->orderBy('name');
        $selectedLead = null;
        if ($request->filled('lead_id')) {
            $selectedLead = (clone $leadQuery)->findOrFail($request->integer('lead_id'));
        }
        $leads = (clone $leadQuery)->limit(500)->get(['id', 'name', 'gym_name', 'assigned_to']);
        $team = $user->canManageTeam() ? User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'role']) : collect([$user]);

        return view('follow-ups.create', compact('leads', 'selectedLead', 'team'));
    }

    public function store(StoreFollowUpRequest $request, ActivityLogger $activityLogger): RedirectResponse
    {
        $data = $request->validated();
        $lead = Lead::query()->visibleTo($request->user())->findOrFail($data['lead_id']);
        $this->authorize('view', $lead);
        if (! $request->user()->canManageTeam() && isset($data['assigned_to']) && (int) $data['assigned_to'] !== $request->user()->id) {
            abort(403);
        }
        $data['assigned_to'] = $request->user()->canManageTeam() ? ($data['assigned_to'] ?? $lead->assigned_to) : $request->user()->id;
        $data['created_by'] = $request->user()->id;
        if ($data['kind'] === 'meeting') {
            $data['meeting_at'] = $data['meeting_at'] ?? $data['due_at'];
        }
        if ($data['kind'] === 'trial') {
            $data['trial_at'] = $data['trial_at'] ?? $data['due_at'];
        }
        $followUp = DB::transaction(function () use ($lead, $data, $request, $activityLogger): FollowUp {
            $followUp = $lead->followUps()->create($data);
            $summary = FollowUp::KINDS[$followUp->kind].' planned for '.$followUp->due_at->format('j M Y, g:i A').'.';
            if ($followUp->meeting_with) $summary .= ' Meeting with '.$followUp->meeting_with.'.';
            $activityLogger->record($lead, $request->user(), 'follow_up_scheduled', 'Follow-up scheduled', $summary, ['follow_up_id' => $followUp->id, 'kind' => $followUp->kind]);
            return $followUp;
        });

        return redirect()->route('leads.show', $lead)->with('success', 'Follow-up added to the calendar.');
    }

    public function complete(Request $request, FollowUp $followUp, ActivityLogger $activityLogger): RedirectResponse
    {
        $this->authorizeFollowUp($request, $followUp);
        abort_if($followUp->completed_at, 409, 'This follow-up is already completed.');
        $data = $request->validate([
            'outcome' => ['required', Rule::in(array_keys(FollowUp::OUTCOMES))],
            'remark' => ['nullable', 'string', 'max:5000'],
            'next_follow_up_at' => ['nullable', 'date', 'after:now'],
            'meeting_at' => ['nullable', 'date'],
            'meeting_with' => ['nullable', 'string', 'max:120', 'required_with:meeting_at'],
            'trial_at' => ['nullable', 'date'],
            'trial_details' => ['nullable', 'string', 'max:2000'],
        ]);
        $lead = $followUp->lead;

        DB::transaction(function () use ($followUp, $lead, $data, $request, $activityLogger): void {
            $followUp->update([
                'outcome' => $data['outcome'], 'remark' => $data['remark'] ?? null, 'completed_at' => now(),
                'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
                'meeting_at' => $data['meeting_at'] ?? $followUp->meeting_at, 'meeting_with' => $data['meeting_with'] ?? $followUp->meeting_with,
                'trial_at' => $data['trial_at'] ?? $followUp->trial_at, 'trial_details' => $data['trial_details'] ?? $followUp->trial_details,
            ]);
            $outcomeLabel = FollowUp::OUTCOMES[$data['outcome']];
            $activityLogger->record($lead, $request->user(), 'follow_up_completed', 'Follow-up completed · '.$outcomeLabel, $data['remark'] ?? null, ['follow_up_id' => $followUp->id, 'outcome' => $data['outcome']]);

            $newStatus = match ($data['outcome']) { 'meeting_fixed' => 'meeting_fixed', 'trial' => 'trial', 'not_interested' => 'lost', default => null };
            if ($newStatus && $lead->status !== $newStatus) {
                $oldStatus = $lead->status;
                $lead->update(['status' => $newStatus]);
                $activityLogger->record($lead, $request->user(), 'status_changed', 'Status changed', 'Moved from '.crm_label($oldStatus).' to '.crm_label($newStatus).' after a follow-up.', ['from' => $oldStatus, 'to' => $newStatus]);
            }

            if (! empty($data['next_follow_up_at'])) {
                $next = $lead->followUps()->create(['assigned_to' => $followUp->assigned_to, 'created_by' => $request->user()->id, 'kind' => 'follow_up', 'due_at' => $data['next_follow_up_at']]);
                $activityLogger->record($lead, $request->user(), 'follow_up_scheduled', 'Next follow-up scheduled', 'Next touchpoint set for '.$next->due_at->format('j M Y, g:i A').'.', ['follow_up_id' => $next->id]);
            }
        });

        return back()->with('success', 'Outcome saved to the activity timeline.');
    }

    public function destroy(Request $request, FollowUp $followUp): RedirectResponse
    {
        $this->authorizeFollowUp($request, $followUp);
        $followUp->delete();
        return back()->with('success', 'Follow-up removed.');
    }

    private function authorizeFollowUp(Request $request, FollowUp $followUp): void
    {
        abort_unless($request->user()->canManageTeam() || $followUp->assigned_to === $request->user()->id, 403);
        $this->authorize('view', $followUp->lead);
    }
}
