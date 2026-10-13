<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['period' => ['nullable', Rule::in(['30', '90', '365'])]]);
        $period = (int) ($validated['period'] ?? 30);
        $today = now($request->user()->timezone ?: config('app.timezone'))->endOfDay();
        $from = $today->copy()->subDays($period - 1)->startOfDay();
        $to = $today;
        $base = Lead::query()->visibleTo($request->user())->whereBetween('created_at', [$from, $to]);
        $total = (clone $base)->count();
        $statusCounts = (clone $base)->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $sourceCounts = (clone $base)->selectRaw('source, COUNT(*) as aggregate')->groupBy('source')->orderByDesc('aggregate')->get()->map(fn ($row) => ['label' => $row->source ?: 'Other', 'count' => (int) $row->aggregate]);
        $sourceRows = $sourceCounts->take(6)->values();
        $statusRows = collect(Lead::STATUSES)->map(fn (string $label, string $key) => ['label' => $label, 'count' => (int) ($statusCounts[$key] ?? 0), 'key' => $key])->filter(fn ($row) => $row['count'] > 0)->values();
        $statusMax = max(1, (int) $statusRows->max('count'));
        $sourceMax = max(1, (int) $sourceRows->max('count'));
        $priorityCounts = (clone $base)->selectRaw('priority, COUNT(*) as aggregate')->groupBy('priority')->pluck('aggregate', 'priority');
        $priorityRows = collect(Lead::PRIORITIES)->map(fn (string $label, string $key) => ['label' => $label, 'key' => $key, 'count' => (int) ($priorityCounts[$key] ?? 0)])->values();
        $priorityMax = max(1, (int) $priorityRows->max('count'));
        $converted = (int) ($statusCounts['converted'] ?? 0);
        $conversionRate = $total > 0 ? round(($converted / $total) * 100, 1) : 0;
        $activeLeads = $total - $converted - (int) ($statusCounts['lost'] ?? 0);

        $touchpoints = FollowUp::query()->visibleTo($request->user())->whereBetween('due_at', [$from, $to]);
        $followUpTotal = (clone $touchpoints)->count();
        $followUpsCompleted = (clone $touchpoints)->whereNotNull('completed_at')->count();
        $followUpsOpen = (clone $touchpoints)->whereNull('completed_at')->count();
        $followUpsOverdue = (clone $touchpoints)->open()->where('due_at', '<', now())->count();
        $followUpRate = $followUpTotal > 0 ? (int) round(($followUpsCompleted / $followUpTotal) * 100) : 0;
        $events = FollowUp::query()->visibleTo($request->user());
        $meetings = (clone $events)->whereBetween('meeting_at', [$from, $to])->count();
        $trials = (clone $events)->whereBetween('trial_at', [$from, $to])->count();

        return view('reports.index', compact(
            'period', 'from', 'to', 'total', 'sourceRows', 'statusRows', 'statusMax', 'sourceMax', 'priorityRows',
            'priorityMax', 'converted', 'conversionRate', 'activeLeads', 'followUpTotal', 'followUpsCompleted',
            'followUpsOpen', 'followUpsOverdue', 'followUpRate', 'meetings', 'trials'
        ));
    }
}
