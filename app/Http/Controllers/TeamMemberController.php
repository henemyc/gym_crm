<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\GooglePlaceProspect;
use App\Models\GooglePlaceProspectActivity;
use App\Models\Lead;
use App\Models\LeadImport;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamMemberController extends Controller
{
    public function index(Request $request): View
    {
        $team = $this->orderedTeamQuery()->get();
        $summary = $this->teamSummary();
        $viewerTimezone = $request->user()->timezone ?: config('app.timezone');

        return view('settings.users', [
            'team' => $team,
            ...$summary,
            'viewerTimezone' => $viewerTimezone,
        ]);
    }

    public function presence(Request $request): JsonResponse
    {
        $onlineCount = 0;
        $timezone = $request->user()->timezone ?: config('app.timezone');
        $presence = User::query()->get(['id', 'is_active', 'last_activity_at', 'last_login_at'])
            ->mapWithKeys(function (User $member) use (&$onlineCount, $timezone): array {
                $online = $member->is_active && $member->isOnlineNow();
                if ($online) $onlineCount++;

                $presenceClass = $online ? 'presence-online' : ($member->last_activity_at ? 'presence-offline' : 'presence-unknown');
                $label = $online ? 'Online now' : (! $member->is_active ? 'Inactive' : ($member->last_activity_at ? 'Offline' : 'Not seen yet'));
                $detail = $member->last_activity_at
                    ? (($online ? 'Active ' : 'Last seen ').$member->last_activity_at->diffForHumans())
                    : 'No activity recorded';

                return [(string) $member->id => [
                    'class' => $presenceClass,
                    'label' => $label,
                    'detail' => $detail,
                    'has_logged_in' => $member->last_login_at !== null,
                    'login_date' => $member->last_login_at?->timezone($timezone)->format('d M Y') ?? 'Never signed in',
                    'login_time' => $member->last_login_at?->timezone($timezone)->format('g:i A') ?? 'First sign-in pending',
                ]];
            });

        return response()->json(['online_count' => $onlineCount, 'members' => $presence]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $roleRules = $request->user()->isAdmin() ? Rule::in(array_keys(User::ROLES)) : Rule::in(['sales']);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', $roleRules],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);
        $data['is_active'] = true;
        $data['timezone'] = $request->user()->timezone ?: 'Asia/Kolkata';
        $data['notifications_enabled'] = true;
        $member = User::create($data);
        $message = $member->name.' has been added to the workspace.';

        if ($request->expectsJson()) {
            return response()->json($this->teamResponse($request, $message), 201);
        }

        return redirect()->route('settings.users')->with('success', $message);
    }

    public function update(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $currentUser = $request->user();
        abort_unless($currentUser->canManageTeam(), 403);
        abort_unless($currentUser->isAdmin() || ($user->role === 'sales' && $user->id !== $currentUser->id), 403);

        $roleRules = $currentUser->isAdmin() ? Rule::in(array_keys(User::ROLES)) : Rule::in(['sales']);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', $roleRules],
            'is_active' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($user, $data): void {
            if ($user->isAdmin() && ($data['role'] !== 'admin' || ! $data['is_active'])) {
                $activeAdminIds = User::query()
                    ->where('role', 'admin')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->pluck('id');
                abort_if($activeAdminIds->count() <= 1, 422, 'At least one active admin is required.');
            }

            $user->update($data);
        });
        $message = 'Team member details and access were updated.';

        if ($request->expectsJson()) {
            return response()->json($this->teamResponse($request, $message));
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, User $user, ActivityLogger $activityLogger): RedirectResponse|JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor->canManageTeam(), 403);
        abort_if($user->id === $actor->id, 422, 'You cannot remove your own account.');
        abort_unless($actor->isAdmin() || $user->role === 'sales', 403);

        DB::transaction(function () use ($actor, $user, $activityLogger): void {
            if ($user->role === 'admin' && $user->is_active) {
                $activeAdminIds = User::query()
                    ->where('role', 'admin')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->pluck('id');
                abort_if($activeAdminIds->count() <= 1, 422, 'At least one active admin is required.');
            }

            Lead::withTrashed()->where('assigned_to', $user->id)->orderBy('id')->chunkById(200, function ($leads) use ($actor, $user, $activityLogger): void {
                foreach ($leads as $lead) {
                    $lead->update(['assigned_to' => $actor->id]);
                    $activityLogger->record(
                        $lead,
                        $actor,
                        'assigned',
                        'Lead reassigned',
                        'Reassigned to '.$actor->name.' after '.$user->name.' was removed from the workspace.'
                    );
                }
            });

            FollowUp::query()->where('assigned_to', $user->id)->orderBy('id')->chunkById(200, function ($followUps) use ($actor, $user, $activityLogger): void {
                $leads = Lead::withTrashed()->whereIn('id', $followUps->pluck('lead_id'))->get()->keyBy('id');
                foreach ($followUps as $followUp) {
                    $followUp->update(['assigned_to' => $actor->id]);
                    $lead = $leads->get($followUp->lead_id);
                    if ($lead) {
                        $activityLogger->record(
                            $lead,
                            $actor,
                            'follow_up_reassigned',
                            'Follow-up reassigned',
                            'Reassigned to '.$actor->name.' after '.$user->name.' was removed from the workspace.',
                            ['follow_up_id' => $followUp->id]
                        );
                    }
                }
            });
            LeadImport::query()->where('user_id', $user->id)->update([
                'user_id' => $actor->id,
                'updated_at' => now(),
            ]);
            GooglePlaceProspect::query()->where('assigned_to', $user->id)->orderBy('id')->chunkById(200, function ($prospects) use ($actor, $user): void {
                foreach ($prospects as $prospect) {
                    $previousAssignee = $prospect->assigned_to;
                    $prospect->update(['assigned_to' => $actor->id]);
                    GooglePlaceProspectActivity::query()->create([
                        'prospect_id' => $prospect->id,
                        'actor_id' => $actor->id,
                        'event' => 'reassigned',
                        'from_assigned_to' => $previousAssignee,
                        'to_assigned_to' => $actor->id,
                    ]);
                }
            });
            $user->delete();
        });

        $message = $user->name.' was removed. Assigned leads, follow-ups, and Google prospects were transferred to you.';
        if ($request->expectsJson()) {
            return response()->json($this->teamResponse($request, $message));
        }

        return redirect()->route('settings.users')->with('success', $message);
    }

    private function orderedTeamQuery(): Builder
    {
        return User::query()
            ->withCount('assignedLeads')
            ->orderByRaw("CASE role WHEN 'admin' THEN 0 WHEN 'manager' THEN 1 ELSE 2 END")
            ->orderBy('name');
    }

    private function teamSummary(): array
    {
        $team = User::query()->get(['id', 'role', 'is_active', 'last_activity_at']);

        return [
            'teamCount' => $team->count(),
            'activeCount' => $team->where('is_active', true)->count(),
            'onlineCount' => $team->filter(fn (User $member): bool => $member->is_active && $member->isOnlineNow())->count(),
            'salesCount' => $team->where('role', 'sales')->where('is_active', true)->count(),
        ];
    }

    private function teamResponse(Request $request, string $message): array
    {
        $viewerTimezone = $request->user()->timezone ?: config('app.timezone');

        return [
            'message' => $message,
            'rows_html' => view('settings.partials.member-rows', [
                'team' => $this->orderedTeamQuery()->get(),
                'viewerTimezone' => $viewerTimezone,
            ])->render(),
            'summary' => $this->teamSummary(),
        ];
    }
}
