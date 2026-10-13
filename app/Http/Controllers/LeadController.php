<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\GooglePlaceProspect;
use App\Models\GooglePlaceProspectActivity;
use App\Models\Lead;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Lead::class);
        $user = $request->user();
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:120'],
            'priority' => ['nullable', Rule::in(array_keys(Lead::PRIORITIES))],
            'status' => ['nullable', Rule::in(array_keys(Lead::STATUSES))],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'assigned_to' => ['nullable', 'integer'],
            'per_page' => ['nullable', Rule::in(['15', '30', '50'])],
        ]);

        $query = Lead::query()->visibleTo($user)->applyFilters($filters)->with('assignee:id,name')->latest();
        $leads = $query->paginate((int) ($filters['per_page'] ?? 15))->withQueryString();
        $activeFilters = collect($filters)->except('per_page')->filter(fn ($value) => filled($value));

        if ($request->expectsJson()) {
            return response()->json([
                'rows' => view('leads.partials.rows', compact('leads', 'activeFilters'))->render(),
                'footer' => view('leads.partials.footer', compact('leads'))->render(),
                'total' => $leads->total(),
                'count' => $leads->count(),
            ]);
        }

        $sources = Lead::query()->visibleTo($user)->whereNotNull('source')->distinct()->orderBy('source')->pluck('source');
        $locations = Lead::query()->visibleTo($user)->whereNotNull('location')->distinct()->orderBy('location')->pluck('location');
        $team = $user->canManageTeam() ? User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'role']) : collect([$user]);
        $placeProspectQuery = GooglePlaceProspect::query()
            ->visibleTo($user)
            ->whereNull('lead_id')
            ->where('status', '!=', 'converted');
        $placeProspects = (clone $placeProspectQuery)
            ->with(['assignee:id,name,role,is_active', 'lead:id,name'])
            ->latest('updated_at')
            ->paginate(25, ['*'], 'prospect_page')
            ->appends($request->except('page'));
        $placeProspectSummary = ['total' => (clone $placeProspectQuery)->count()];
        $showPlaceAssignmentNotice = $request->boolean('places_assigned');

        return view('leads.index', compact(
            'leads', 'filters', 'sources', 'locations', 'team', 'activeFilters',
            'placeProspects', 'placeProspectSummary', 'showPlaceAssignmentNotice'
        ));
    }

    public function create(Request $request): View|JsonResponse
    {
        $this->authorize('create', Lead::class);
        $user = $request->user();
        $team = $user->canManageTeam() ? User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'role']) : collect([$user]);

        $data = ['lead' => new Lead(['priority' => 'warm', 'status' => 'new', 'source' => 'Website', 'assigned_to' => $user->id]), 'team' => $team, 'mode' => 'create'];

        if ($request->expectsJson()) {
            return response()->json(['html' => view('leads.partials.modal-form', $data)->render()]);
        }

        return view('leads.form', $data);
    }

    public function store(StoreLeadRequest $request, ActivityLogger $activityLogger): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Lead::class);
        $data = $request->validated();
        $prospectId = isset($data['place_prospect_id']) ? (int) $data['place_prospect_id'] : null;
        unset($data['place_prospect_id'], $data['independent_contact_verified']);
        $data['assigned_to'] = $request->user()->canManageTeam()
            ? ($request->exists('assigned_to') ? $data['assigned_to'] : $request->user()->id)
            : $request->user()->id;
        $data['created_by'] = $request->user()->id;

        $lead = DB::transaction(function () use ($data, $prospectId, $request, $activityLogger): Lead {
            $prospect = null;
            if ($prospectId) {
                $prospect = GooglePlaceProspect::query()->whereKey($prospectId)->lockForUpdate()->firstOrFail();
                abort_unless($request->user()->canManageTeam() || (int) $prospect->assigned_to === (int) $request->user()->id, 403);
                abort_if($prospect->isConverted(), 409, 'This prospect has already been converted to a CRM lead.');
            }

            $lead = Lead::create($data);
            $activityLogger->record($lead, $request->user(), 'lead_created', 'Lead added to pipeline', "Added from {$lead->source}.");
            if (filled($lead->notes)) {
                $activityLogger->record($lead, $request->user(), 'note', 'Initial notes added', $lead->notes);
            }
            if ($prospect) {
                $previousStatus = $prospect->status;
                $prospect->update(['lead_id' => $lead->id, 'status' => 'converted']);
                GooglePlaceProspectActivity::query()->create([
                    'prospect_id' => $prospect->id,
                    'actor_id' => $request->user()->id,
                    'event' => 'converted',
                    'from_status' => $previousStatus,
                    'to_status' => 'converted',
                ]);
            }

            return $lead;
        });

        $message = $prospectId
            ? 'Verified contact details were added as a CRM lead.'
            : 'Lead added to your pipeline.';
        if ($request->expectsJson()) {
            $payload = ['message' => $message, 'id' => $lead->id, 'url' => route('leads.show', $lead)];
            if ($prospectId) {
                $payload['place_prospect'] = ['id' => $prospectId, 'status' => 'converted', 'lead_url' => route('leads.show', $lead)];
            }

            return response()->json($payload, 201);
        }

        return redirect()->route('leads.show', $lead)->with('success', $message);
    }

    public function show(Lead $lead, Request $request): View|JsonResponse
    {
        $this->authorize('view', $lead);
        if ($request->expectsJson()) {
            $lead->load('assignee:id,name,email,role');
            return response()->json(['html' => view('leads.partials.preview', compact('lead'))->render()]);
        }

        $lead->load([
            'assignee:id,name,email,role',
            'creator:id,name',
            'followUps.assignee:id,name',
            'activities.user:id,name',
        ]);

        return view('leads.show', ['lead' => $lead, 'activities' => $lead->activities, 'followUps' => $lead->followUps]);
    }

    public function edit(Lead $lead, Request $request): View|JsonResponse
    {
        $this->authorize('update', $lead);
        $user = $request->user();
        $team = $user->canManageTeam() ? User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'role']) : collect([$user]);

        $data = ['lead' => $lead, 'team' => $team, 'mode' => 'edit'];

        if ($request->expectsJson()) {
            return response()->json(['html' => view('leads.partials.modal-form', $data)->render()]);
        }

        return view('leads.form', $data);
    }

    public function update(UpdateLeadRequest $request, Lead $lead, ActivityLogger $activityLogger): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $lead);
        $data = $request->validated();
        if (! $request->user()->canManageTeam()) {
            $data['assigned_to'] = $lead->assigned_to;
        }

        $previous = $lead->only(['status', 'assigned_to']);
        $lead->fill($data);
        $changes = $lead->getDirty();

        DB::transaction(function () use ($lead, $changes, $previous, $request, $activityLogger): void {
            $lead->save();
            if (array_key_exists('status', $changes)) {
                $activityLogger->record($lead, $request->user(), 'status_changed', 'Status changed', 'Moved from '.crm_label($previous['status']).' to '.crm_label($lead->status).'.', ['from' => $previous['status'], 'to' => $lead->status]);
            }
            if (array_key_exists('assigned_to', $changes)) {
                $assignee = $lead->assignee()->value('name') ?: 'Unassigned';
                $activityLogger->record($lead, $request->user(), 'assigned', 'Lead reassigned', "Assigned to {$assignee}.");
            }
            $otherChanges = array_diff(array_keys($changes), ['status', 'assigned_to']);
            if ($otherChanges) {
                $activityLogger->record($lead, $request->user(), 'lead_updated', 'Lead details updated', 'Updated: '.implode(', ', array_map(fn ($field) => str_replace('_', ' ', $field), $otherChanges)).'.');
            }
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Lead details updated.', 'id' => $lead->id, 'url' => route('leads.show', $lead)]);
        }

        return redirect()->route('leads.show', $lead)->with('success', 'Lead details updated.');
    }

    public function destroy(Lead $lead, Request $request, ActivityLogger $activityLogger): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $lead);
        $activityLogger->record($lead, $request->user(), 'lead_deleted', 'Lead moved to deleted records');
        $lead->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Lead moved to deleted records.', 'id' => $lead->id]);
        }

        return redirect()->route('leads.index')->with('success', 'Lead moved to deleted records.');
    }

    public function bulkDestroy(Request $request, ActivityLogger $activityLogger): RedirectResponse|JsonResponse
    {
        $this->authorize('viewAny', Lead::class);
        $validated = $request->validate(['ids' => ['required', 'array', 'min:1', 'max:100'], 'ids.*' => ['required', 'integer', 'distinct']]);
        $leads = Lead::query()->visibleTo($request->user())->whereIn('id', $validated['ids'])->get();
        abort_if($leads->count() !== count($validated['ids']), 403, 'One or more selected leads are unavailable.');
        foreach ($leads as $lead) {
            $this->authorize('delete', $lead);
        }
        DB::transaction(function () use ($leads, $request, $activityLogger): void {
            foreach ($leads as $lead) {
                $activityLogger->record($lead, $request->user(), 'lead_deleted', 'Lead moved to deleted records');
                $lead->delete();
            }
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => $leads->count().' leads moved to deleted records.', 'ids' => array_values(array_map('intval', $validated['ids']))]);
        }

        return redirect()->route('leads.index')->with('success', $leads->count().' leads moved to deleted records.');
    }

    public function updateStatus(Request $request, Lead $lead, ActivityLogger $activityLogger): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $lead);
        $data = $request->validate(['status' => ['required', Rule::in(array_keys(Lead::STATUSES))]]);
        $previous = $lead->status;
        if ($previous !== $data['status']) {
            $lead->update(['status' => $data['status']]);
            $activityLogger->record($lead, $request->user(), 'status_changed', 'Status changed', 'Moved from '.crm_label($previous).' to '.crm_label($lead->status).'.', ['from' => $previous, 'to' => $lead->status]);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Lead status updated.', 'status' => $lead->status]);
        }

        return back()->with('success', 'Lead status updated.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Lead::class);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'source' => ['nullable', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:120'], 'priority' => ['nullable', Rule::in(array_keys(Lead::PRIORITIES))],
            'status' => ['nullable', Rule::in(array_keys(Lead::STATUSES))], 'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'], 'assigned_to' => ['nullable', 'integer'],
        ]);
        $query = Lead::query()->visibleTo($request->user())->applyFilters($filters)->with('assignee:id,name')->orderBy('id');
        $filename = 'gymxbook-leads-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Name', 'Gym Name', 'Phone', 'Location', 'Source', 'Priority', 'Status', 'Assigned To', 'Notes', 'Created At']);
            $query->chunkById(500, function ($leads) use ($output): void {
                foreach ($leads as $lead) {
                    $values = [$lead->name, $lead->gym_name, $lead->phone, $lead->location, $lead->source, crm_label($lead->priority), crm_label($lead->status), $lead->assignee?->name, $lead->notes, $lead->created_at?->toDateTimeString()];
                    fputcsv($output, array_map(fn ($value) => $this->spreadsheetSafe($value), $values));
                }
            });
            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function spreadsheetSafe(mixed $value): string
    {
        $value = (string) ($value ?? '');
        return preg_match('/^[\s]*[=+@\-]/', $value) ? "'{$value}" : $value;
    }
}
