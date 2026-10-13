@extends('layouts.app')
@section('title', 'Calendar')
@section('page-name', 'Calendar')

@section('content')
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> PLAN THE NEXT CONVERSATION</div><h1>Calendar</h1><p class="page-description">Follow-ups, meetings, and trials in one shared view.</p></div><a class="btn btn-primary" href="{{ route('follow-ups.create') }}"><x-icon name="plus" size="17" /> Schedule touchpoint</a></div>
    <section class="surface-card">
        <div class="calendar-toolbar">
            <div class="calendar-nav"><a class="icon-button" href="{{ route('calendar.index', ['view' => $viewMode, 'date' => $previous]) }}" aria-label="Previous {{ $viewMode }}"><x-icon name="chevron-left" size="18" /></a><h2>{{ $title }}</h2><a class="icon-button" href="{{ route('calendar.index', ['view' => $viewMode, 'date' => $next]) }}" aria-label="Next {{ $viewMode }}"><x-icon name="chevron-right" size="18" /></a><a class="btn btn-secondary btn-sm" href="{{ route('calendar.index', ['view' => $viewMode, 'date' => $today]) }}">Today</a></div>
            <div class="calendar-controls"><div class="calendar-legend"><span><i class="legend-follow"></i> Follow-up</span><span><i class="legend-meeting"></i> Meeting</span><span><i class="legend-trial"></i> Trial</span></div><div class="tab-bar" aria-label="Calendar view">@foreach(['month'=>'Month','week'=>'Week','day'=>'Day'] as $key=>$label)<a class="tab-link {{ $viewMode === $key ? 'active' : '' }}" href="{{ route('calendar.index', ['view'=>$key, 'date'=>$focusDate->toDateString()]) }}">{{ $label }}</a>@endforeach</div></div>
        </div>
        @if ($viewMode === 'day')
            <div class="agenda-list">
                @forelse (($eventsByDate->get($focusDate->toDateString()) ?? collect()) as $event)
                    <div class="agenda-day"><h3>{{ $event['time'] }} · {{ $event['kind_label'] }}</h3><a class="agenda-event" href="{{ route('leads.show', $event['follow_up']->lead) }}"><span class="calendar-event event-{{ $event['type'] }}">{{ $event['title'] }}</span><strong>{{ $event['gym'] }}</strong><small>{{ $event['follow_up']->assignee?->name ?? 'Unassigned' }}</small></a></div>
                @empty
                    <div class="empty-state"><span class="empty-icon"><x-icon name="calendar" size="23" /></span><strong>A little breathing room</strong><p>No touchpoints are scheduled for this day. Add one whenever you’re ready.</p><a class="btn btn-secondary btn-sm" href="{{ route('follow-ups.create') }}"><x-icon name="plus" size="14" /> Schedule touchpoint</a></div>
                @endforelse
            </div>
        @elseif ($viewMode === 'week')
            <div class="calendar-grid calendar-week-view">
                @foreach ($cells as $date)
                    <div class="calendar-week-day"><div class="calendar-week-head {{ $date->toDateString() === $today ? 'is-today' : '' }}"><small>{{ $date->translatedFormat('D') }}</small><strong>{{ $date->format('j') }}</strong></div><div class="calendar-day-events">@forelse (($eventsByDate->get($date->toDateString()) ?? collect())->take(8) as $event)<a class="calendar-event event-{{ $event['type'] }}" href="{{ route('leads.show', $event['follow_up']->lead) }}" title="{{ $event['time'] }} · {{ $event['title'] }}">{{ $event['time'] }} {{ $event['title'] }}</a>@empty<span class="calendar-empty-cell">No plans</span>@endforelse</div></div>
                @endforeach
            </div>
        @else
            <div class="calendar-grid month-grid">
                @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $weekday)<div class="calendar-weekday">{{ $weekday }}</div>@endforeach
                @foreach ($cells as $date)
                    @php $dayEvents = $eventsByDate->get($date->toDateString(), collect()); @endphp
                    <div class="calendar-day {{ $date->month !== $focusDate->month ? 'is-outside' : '' }} {{ $date->toDateString() === $today ? 'is-today' : '' }}"><div class="calendar-day-top"><span class="calendar-day-number">{{ $date->format('j') }}</span>@if($dayEvents->count() > 3)<span class="table-muted">{{ $dayEvents->count() }} events</span>@endif</div><div class="calendar-day-events">@foreach ($dayEvents->take(3) as $event)<a class="calendar-event event-{{ $event['type'] }}" href="{{ route('leads.show', $event['follow_up']->lead) }}" title="{{ $event['time'] }} · {{ $event['title'] }}">{{ $event['time'] }} {{ $event['title'] }}</a>@endforeach @if($dayEvents->count() > 3)<a class="calendar-more" href="{{ route('calendar.index', ['view'=>'day','date'=>$date->toDateString()]) }}">+{{ $dayEvents->count() - 3 }} more</a>@endif</div></div>
                @endforeach
            </div>
        @endif
        <div class="calendar-footer"><span>All times in {{ auth()->user()->timezone ?: 'Asia/Kolkata' }}</span><span class="calendar-legend"><i class="legend-follow"></i> Follow-up <i class="legend-meeting"></i> Meeting <i class="legend-trial"></i> Trial</span></div>
    </section>
@endsection
