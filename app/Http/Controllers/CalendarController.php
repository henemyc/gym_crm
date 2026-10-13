<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'view' => ['nullable', Rule::in(['month', 'week', 'day'])],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $viewMode = $validated['view'] ?? 'month';
        $user = $request->user();
        $timezone = $user->timezone ?: config('app.timezone');
        $focusDate = isset($validated['date']) ? Carbon::parse($validated['date'], $timezone)->startOfDay() : now($timezone)->startOfDay();
        $today = now($timezone)->toDateString();

        if ($viewMode === 'month') {
            $rangeStart = $focusDate->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
            $rangeEnd = $focusDate->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
            $cells = collect();
            for ($date = $rangeStart->copy(); $date->lte($rangeEnd); $date->addDay()) $cells->push($date->copy());
            $title = $focusDate->translatedFormat('F Y');
        } elseif ($viewMode === 'week') {
            $rangeStart = $focusDate->copy()->startOfWeek(Carbon::MONDAY);
            $rangeEnd = $focusDate->copy()->endOfWeek(Carbon::SUNDAY);
            $cells = collect();
            for ($date = $rangeStart->copy(); $date->lte($rangeEnd); $date->addDay()) $cells->push($date->copy());
            $title = $rangeStart->translatedFormat('j M').' – '.$rangeEnd->translatedFormat('j M Y');
        } else {
            $rangeStart = $focusDate->copy()->startOfDay();
            $rangeEnd = $focusDate->copy()->endOfDay();
            $cells = collect([$focusDate->copy()]);
            $title = $focusDate->translatedFormat('l, j F Y');
        }

        $followUps = FollowUp::query()->visibleTo($user)
            ->where(function (Builder $query) use ($rangeStart, $rangeEnd): void {
                $query->whereBetween('due_at', [$rangeStart, $rangeEnd])
                    ->orWhereBetween('meeting_at', [$rangeStart, $rangeEnd])
                    ->orWhereBetween('trial_at', [$rangeStart, $rangeEnd]);
            })
            ->with(['lead:id,name,gym_name,phone,priority,status', 'assignee:id,name'])
            ->orderBy('due_at')->get();

        $events = $this->eventsFor($followUps, $rangeStart, $rangeEnd);
        $eventsByDate = $events->groupBy(fn (array $event) => $event['date']);
        $previous = $focusDate->copy()->subUnit($viewMode === 'month' ? 'month' : ($viewMode === 'week' ? 'week' : 'day'))->toDateString();
        $next = $focusDate->copy()->addUnit($viewMode === 'month' ? 'month' : ($viewMode === 'week' ? 'week' : 'day'))->toDateString();

        return view('calendar.index', compact('viewMode', 'focusDate', 'today', 'rangeStart', 'rangeEnd', 'cells', 'title', 'events', 'eventsByDate', 'previous', 'next'));
    }

    private function eventsFor(Collection $followUps, CarbonInterface $start, CarbonInterface $end): Collection
    {
        $events = collect();
        foreach ($followUps as $followUp) {
            $add = function (?CarbonInterface $date, string $type, string $source) use (&$events, $followUp, $start, $end): void {
                if (! $date || $date->lt($start) || $date->gt($end)) return;
                $events->push([
                    'date' => $date->toDateString(),
                    'time' => $date->format('g:i A'),
                    'datetime' => $date,
                    'type' => $type,
                    'source' => $source,
                    'title' => $followUp->lead?->name ?? 'Gym lead',
                    'gym' => $followUp->lead?->gym_name ?? '',
                    'kind_label' => FollowUp::KINDS[$type] ?? crm_label($type),
                    'follow_up' => $followUp,
                ]);
            };

            $kind = in_array($followUp->kind, ['meeting', 'trial'], true) ? $followUp->kind : 'follow_up';
            $add($followUp->due_at, $kind, 'due_at');
            if ($followUp->meeting_at && (! $followUp->due_at || ! $followUp->meeting_at->equalTo($followUp->due_at) || $kind !== 'meeting')) $add($followUp->meeting_at, 'meeting', 'meeting_at');
            if ($followUp->trial_at && (! $followUp->due_at || ! $followUp->trial_at->equalTo($followUp->due_at) || $kind !== 'trial')) $add($followUp->trial_at, 'trial', 'trial_at');
        }

        return $events->sortBy(fn (array $event) => $event['datetime'])->values();
    }
}
