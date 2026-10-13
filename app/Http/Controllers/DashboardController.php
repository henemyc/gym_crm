<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Lead;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $leads = Lead::query()->visibleTo($user);
        $today = now($user->timezone ?: config('app.timezone'));
        $followUps = FollowUp::query()->visibleTo($user);

        $statusCounts = (clone $leads)->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $priorityCounts = (clone $leads)->selectRaw('priority, COUNT(*) as aggregate')->groupBy('priority')->pluck('aggregate', 'priority');
        $total = (clone $leads)->count();
        $converted = (int) ($statusCounts['converted'] ?? 0);

        $pipeline = collect(Lead::STATUSES)->map(fn (string $label, string $key) => [
            'key' => $key,
            'label' => $label,
            'count' => (int) ($statusCounts[$key] ?? 0),
            'percent' => $total > 0 ? (int) round((($statusCounts[$key] ?? 0) / $total) * 100) : 0,
        ])->values();

        $weeklyTrend = collect(CarbonPeriod::create($today->copy()->subDays(6)->startOfDay(), '1 day', $today->copy()->endOfDay()))
            ->map(fn ($day) => [
                'label' => $day->translatedFormat('D'),
                'date' => $day->toDateString(),
                'count' => (clone $leads)->whereDate('created_at', $day->toDateString())->count(),
            ])->values();

        $upcoming = (clone $followUps)->open()
            ->whereBetween('due_at', [$today->copy()->startOfDay(), $today->copy()->addDays(7)->endOfDay()])
            ->with(['lead:id,name,gym_name,phone,priority,status', 'assignee:id,name'])
            ->orderBy('due_at')->limit(6)->get();

        $recentLeads = (clone $leads)->with('assignee:id,name')->latest()->limit(6)->get();
        $todayFollowUps = (clone $followUps)->open()->whereDate('due_at', $today->toDateString())->count();
        $meetings = (clone $followUps)->whereNotNull('meeting_at')->whereBetween('meeting_at', [$today->copy()->startOfDay(), $today->copy()->addDays(7)->endOfDay()])->count();
        $trials = (clone $followUps)->whereNotNull('trial_at')->whereBetween('trial_at', [$today->copy()->startOfDay(), $today->copy()->addDays(7)->endOfDay()])->count();
        $sourceCounts = (clone $leads)->selectRaw('source, COUNT(*) as aggregate')->groupBy('source')->orderByDesc('aggregate')->limit(5)->pluck('aggregate', 'source');
        $hotCount = (int) ($priorityCounts['hot'] ?? 0);
        $openCount = $total - $converted - (int) ($statusCounts['lost'] ?? 0);

        return view('dashboard.index', compact(
            'today', 'total', 'statusCounts', 'priorityCounts', 'converted', 'pipeline', 'weeklyTrend',
            'upcoming', 'recentLeads', 'todayFollowUps', 'meetings', 'trials', 'sourceCounts', 'hotCount', 'openCount'
        ));
    }
}
