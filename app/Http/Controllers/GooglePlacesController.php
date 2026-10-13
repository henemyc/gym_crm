<?php

namespace App\Http\Controllers;

use App\Exceptions\GooglePlacesException;
use App\Models\GooglePlaceProspect;
use App\Models\GooglePlaceProspectActivity;
use App\Models\Lead;
use App\Models\User;
use App\Models\WorkspaceSetting;
use App\Services\GooglePlacesClient;
use App\Services\GooglePlacesSearchService;
use App\Services\GooglePlacesUsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GooglePlacesController extends Controller
{
    public function index(Request $request, GooglePlacesUsageService $usage): View|JsonResponse
    {
        $actor = $request->user();
        $pageName = $request->query->has('prospect_page') ? 'prospect_page' : 'page';
        $openOnly = $request->boolean('open_only');
        $page = max(1, min(100000, (int) $request->query($pageName, 1)));
        $queue = $this->queueData($actor, $page, $pageName, $openOnly);

        if ($request->expectsJson()) {
            return $this->noStoreJson([
                'rows_html' => $queue['rows_html'],
                'pagination_html' => $queue['pagination_html'],
                'summary' => $queue['summary'],
                'open_only' => $openOnly,
            ]);
        }

        $team = $queue['team'];
        $configured = $actor->canManageTeam() && WorkspaceSetting::secret('google_places_api_key') !== null;
        $usageOverview = $actor->isAdmin() ? $usage->overview() : null;
        $queue['prospects']->appends($request->query());

        return view('prospecting.index', [
            'prospects' => $queue['prospects'],
            'team' => $team,
            'canSearch' => $actor->canManageTeam(),
            'googlePlacesConfigured' => $configured,
            'usageOverview' => $usageOverview,
            'summary' => $queue['summary'],
        ]);
    }

    public function search(Request $request, GooglePlacesSearchService $searchService): JsonResponse
    {
        abort_unless($request->user()->canManageTeam(), 403);
        $data = $request->validate([
            'location' => ['required', 'string', 'min:2', 'max:120'],
            'radius_km' => ['required', 'numeric', 'min:'.config('google_places.radius_min_km', 1), 'max:'.config('google_places.radius_max_km', 50)],
            'page_state' => ['nullable', 'string', 'max:8000'],
        ]);

        try {
            $result = $searchService->search(
                trim($data['location']),
                (float) $data['radius_km'],
                $request->user(),
                $data['page_state'] ?? null
            );
        } catch (GooglePlacesException $exception) {
            return $this->noStoreJson(['message' => $exception->getMessage()], $exception->httpStatus);
        }

        return $this->noStoreJson([
            ...$result,
            'location_label' => trim($data['location']),
            'message' => count($result['results'])
                ? count($result['results']).' gym '.(count($result['results']) === 1 ? 'result' : 'results').' returned for this page.'
                : 'No matching gyms were returned for this page.',
        ]);
    }

    public function assign(Request $request, GooglePlacesSearchService $searchService): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor->canManageTeam(), 403);
        $data = $request->validate([
            'candidates' => ['required', 'array', 'min:1', 'max:40'],
            'candidates.*' => ['required', 'string', 'max:2048', 'distinct'],
            'assigned_to' => ['required', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
        ]);

        $placeIds = array_values(array_unique(array_map(
            fn (string $token): string => $searchService->resolveSelectionToken($token, $actor),
            $data['candidates']
        )));
        abort_if(count($placeIds) > 40, 422, 'Select no more than 40 places at a time.');

        $added = 0;
        $duplicates = 0;
        DB::transaction(function () use ($placeIds, $data, $actor, &$added, &$duplicates): void {
            foreach ($placeIds as $placeId) {
                $prospect = GooglePlaceProspect::query()->firstOrCreate(
                    ['place_id' => $placeId],
                    ['assigned_to' => (int) $data['assigned_to'], 'created_by' => $actor->id, 'status' => 'new']
                );

                if (! $prospect->wasRecentlyCreated) {
                    $duplicates++;
                    continue;
                }

                $added++;
                $this->recordActivity($prospect, $actor, 'created', null, null, (int) $data['assigned_to'], 'new');
            }
        });

        $queue = $this->queueData($actor, 1);
        $message = $added
            ? $added.' '.($added === 1 ? 'prospect was' : 'prospects were').' assigned. Google details are not stored in GymXBook.'
            : 'Those places are already assigned; no duplicate prospects were added.';
        if ($duplicates > 0 && $added > 0) {
            $message .= ' '.$duplicates.' duplicate '.($duplicates === 1 ? 'was' : 'were').' skipped.';
        }

        return $this->noStoreJson([
            'message' => $message,
            'added_count' => $added,
            'duplicate_count' => $duplicates,
            'rows_html' => $queue['rows_html'],
            'pagination_html' => $queue['pagination_html'],
            'summary' => $queue['summary'],
            'assigned_place_ids' => $placeIds,
            'leads_url' => route('leads.index', ['places_assigned' => 1]).'#google-maps-prospects',
        ], $added ? 201 : 200);
    }

    public function details(Request $request, GooglePlaceProspect $prospect, GooglePlacesClient $client): JsonResponse
    {
        $this->authorizeProspect($request->user(), $prospect);

        try {
            $place = $client->details($prospect->place_id);
        } catch (GooglePlacesException $exception) {
            return $this->noStoreJson(['message' => $exception->getMessage()], $exception->httpStatus);
        }

        return $this->noStoreJson([
            'place' => $place,
            'notice' => 'Live Google Maps information. GymXBook does not save these Google-provided fields.',
        ]);
    }

    public function updateStatus(Request $request, GooglePlaceProspect $prospect): JsonResponse
    {
        $actor = $request->user();
        $this->authorizeProspect($actor, $prospect);
        abort_if($prospect->isConverted(), 422, 'A converted prospect is locked to its CRM lead.');
        $data = $request->validate(['status' => ['required', Rule::in(['new', 'contacted', 'qualified', 'not_interested'])]]);
        $previous = $prospect->status;

        DB::transaction(function () use ($prospect, $actor, $data, $previous): void {
            $prospect->update(['status' => $data['status']]);
            if ($previous !== $data['status']) {
                $this->recordActivity($prospect, $actor, 'status_changed', null, $previous, null, $data['status']);
            }
        });

        $prospect->refresh()->load('assignee:id,name,role,is_active', 'lead:id,name');
        return $this->noStoreJson([
            'message' => $previous === $data['status'] ? 'Prospect status is unchanged.' : 'Prospect status updated.',
            'row_html' => $this->prospectRowHtml($prospect, $actor->canManageTeam()),
            'summary' => $this->prospectSummary($actor),
        ]);
    }

    public function reassign(Request $request, GooglePlaceProspect $prospect): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor->canManageTeam(), 403);
        $data = $request->validate([
            'assigned_to' => ['required', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
        ]);
        $previous = $prospect->assigned_to;
        $assignee = (int) $data['assigned_to'];

        DB::transaction(function () use ($prospect, $actor, $previous, $assignee): void {
            $prospect->update(['assigned_to' => $assignee]);
            if ((int) $previous !== $assignee) {
                $this->recordActivity($prospect, $actor, 'reassigned', $previous ? (int) $previous : null, null, $assignee);
            }
        });

        $prospect->refresh()->load('assignee:id,name,role,is_active', 'lead:id,name');
        return $this->noStoreJson([
            'message' => (int) $previous === $assignee ? 'Prospect is already assigned to that teammate.' : 'Prospect reassigned.',
            'row_html' => $this->prospectRowHtml($prospect, true),
            'summary' => $this->prospectSummary($actor),
        ]);
    }

    public function conversionForm(Request $request, GooglePlaceProspect $prospect): JsonResponse
    {
        $this->authorizeProspect($request->user(), $prospect);
        abort_if($prospect->isConverted(), 409, 'This prospect has already been converted to a CRM lead.');

        $actor = $request->user();
        $team = $actor->canManageTeam()
            ? User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'role'])
            : collect([$actor]);
        $lead = new Lead([
            'priority' => 'warm',
            'status' => 'new',
            'source' => 'Google Maps',
            'assigned_to' => $prospect->assigned_to ?: $actor->id,
        ]);
        $html = view('leads.partials.modal-form', [
            'lead' => $lead,
            'team' => $team,
            'mode' => 'create',
            'placeProspectId' => $prospect->id,
        ])->render();

        return $this->noStoreJson(['html' => $html]);
    }

    private function prospectSummary(User $actor, bool $openOnly = false): array
    {
        $summaryQuery = GooglePlaceProspect::query()->visibleTo($actor);
        if ($openOnly) {
            $summaryQuery->whereNull('lead_id')->where('status', '!=', 'converted');
        }

        return [
            'total' => (clone $summaryQuery)->count(),
            'active' => (clone $summaryQuery)->whereIn('status', ['new', 'contacted', 'qualified'])->count(),
            'converted' => (clone $summaryQuery)->where('status', 'converted')->count(),
        ];
    }

    private function queueData(User $actor, int $page, string $pageName = 'page', bool $openOnly = false): array
    {
        $query = GooglePlaceProspect::query()
            ->visibleTo($actor)
            ->with(['assignee:id,name,role,is_active', 'lead:id,name'])
            ->when($openOnly, fn ($builder) => $builder->whereNull('lead_id')->where('status', '!=', 'converted'))
            ->latest('updated_at');
        $prospects = $query->paginate(25, ['*'], $pageName, $page);
        $summary = $this->prospectSummary($actor, $openOnly);
        $canManageTeam = $actor->canManageTeam();
        $team = $canManageTeam
            ? User::query()->where('is_active', true)->orderByRaw("CASE role WHEN 'admin' THEN 0 WHEN 'manager' THEN 1 ELSE 2 END")->orderBy('name')->get(['id', 'name', 'role'])
            : collect();

        return [
            'prospects' => $prospects,
            'summary' => $summary,
            'team' => $team,
            'rows_html' => view('prospecting.partials.prospect-rows', compact('prospects', 'canManageTeam', 'team'))->render(),
            'pagination_html' => view('prospecting.partials.prospect-pagination', compact('prospects'))->render(),
        ];
    }

    private function prospectRowHtml(GooglePlaceProspect $prospect, bool $canManageTeam): string
    {
        $team = $canManageTeam
            ? User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'role'])
            : collect();

        return view('prospecting.partials.prospect-row', compact('prospect', 'canManageTeam', 'team'))->render();
    }

    private function authorizeProspect(User $actor, GooglePlaceProspect $prospect): void
    {
        abort_unless($actor->canManageTeam() || (int) $prospect->assigned_to === (int) $actor->id, 403);
    }

    private function recordActivity(
        GooglePlaceProspect $prospect,
        User $actor,
        string $event,
        ?int $fromAssignee,
        ?string $fromStatus,
        ?int $toAssignee = null,
        ?string $toStatus = null
    ): void {
        GooglePlaceProspectActivity::query()->create([
            'prospect_id' => $prospect->id,
            'actor_id' => $actor->id,
            'event' => $event,
            'from_assigned_to' => $fromAssignee,
            'to_assigned_to' => $toAssignee,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
        ]);
    }

    private function noStoreJson(array $payload, int $status = 200): JsonResponse
    {
        return response()->json($payload, $status)
            ->header('Cache-Control', 'no-store, private, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
