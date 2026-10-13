@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-name', 'Dashboard')

@section('content')
    @php
        $hour = now(auth()->user()->timezone ?: config('app.timezone'))->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $conversionRate = $total > 0 ? round(($converted / $total) * 100, 1) : 0;
    @endphp
    <div class="page-heading dashboard-heading">
        <div><div class="eyebrow"><span class="eyebrow-dot"></span> SALES OVERVIEW <span class="heading-date">{{ $today->translatedFormat('l, j F Y') }}</span></div><h1>{{ $greeting }}, {{ auth()->user()->name ? explode(' ', auth()->user()->name)[0] : 'there' }} <span class="wave">✦</span></h1><p class="page-description">Here’s what’s moving in your gym sales pipeline today.</p></div>
        <div class="heading-actions"><a class="btn btn-secondary" href="{{ route('reports.index') }}"><x-icon name="chart" size="17" /> View reports</a><a class="btn btn-primary" href="{{ route('leads.create') }}" data-lead-form-url="{{ route('leads.create') }}"><x-icon name="plus" size="18" /> Add lead</a></div>
    </div>

    <section class="metric-grid" aria-label="Lead metrics">
        <article class="metric-card metric-featured">
            <div class="metric-top"><span class="metric-label">Total leads</span><span class="metric-icon metric-icon-orange"><x-icon name="users" size="19" /></span></div>
            <div class="metric-value">{{ number_format($total) }}</div>
            <div class="metric-foot"><span class="metric-hint">Across your workspace</span><span class="metric-caption">All leads</span></div>
        </article>
        <article class="metric-card">
            <div class="metric-top"><span class="metric-label">New leads</span><span class="metric-icon metric-icon-blue"><x-icon name="sparkles" size="19" /></span></div>
            <div class="metric-value">{{ number_format((int) ($statusCounts['new'] ?? 0)) }}</div>
            <div class="metric-foot"><span class="metric-hint">Fresh opportunities</span><a href="{{ route('leads.index', ['status' => 'new']) }}" class="metric-link">View <x-icon name="arrow-right" size="14" /></a></div>
        </article>
        <article class="metric-card">
            <div class="metric-top"><span class="metric-label">Hot leads</span><span class="metric-icon metric-icon-red"><x-icon name="flame" size="19" /></span></div>
            <div class="metric-value">{{ number_format($hotCount) }}</div>
            <div class="metric-foot"><span class="metric-hint">Ready for a follow-up</span><a href="{{ route('leads.index', ['priority' => 'hot']) }}" class="metric-link">Prioritize <x-icon name="arrow-right" size="14" /></a></div>
        </article>
        <article class="metric-card">
            <div class="metric-top"><span class="metric-label">Follow-ups today</span><span class="metric-icon metric-icon-green"><x-icon name="clock" size="19" /></span></div>
            <div class="metric-value">{{ number_format($todayFollowUps) }}</div>
            <div class="metric-foot"><span class="metric-hint">Make every touchpoint count</span><a href="{{ route('follow-ups.index', ['view' => 'today']) }}" class="metric-link">Open list <x-icon name="arrow-right" size="14" /></a></div>
        </article>
    </section>

    <section class="quick-stat-row" aria-label="Pipeline highlights">
        <div class="quick-stat"><span class="quick-stat-dot dot-blue"></span><span>Meetings</span><strong>{{ $meetings }}</strong><small>next 7 days</small></div>
        <span class="quick-stat-separator"></span>
        <div class="quick-stat"><span class="quick-stat-dot dot-purple"></span><span>Trials</span><strong>{{ $trials }}</strong><small>next 7 days</small></div>
        <span class="quick-stat-separator"></span>
        <div class="quick-stat"><span class="quick-stat-dot dot-green"></span><span>Converted</span><strong>{{ number_format($converted) }}</strong><small>{{ $conversionRate }}% conversion</small></div>
        <span class="quick-stat-separator"></span>
        <div class="quick-stat"><span class="quick-stat-dot dot-gray"></span><span>Lost</span><strong>{{ number_format((int) ($statusCounts['lost'] ?? 0)) }}</strong><small>closed leads</small></div>
        <div class="quick-stat-end"><span class="open-pipeline-label">{{ number_format($openCount) }} active in pipeline</span><span class="open-pipeline-track"><i style="width:{{ $total ? min(100, round(($openCount / $total) * 100)) : 0 }}%"></i></span></div>
    </section>

    <div class="dashboard-grid dashboard-grid-primary">
        <section class="surface-card pipeline-card">
            <div class="card-heading"><div><h2>Lead pipeline</h2><p>See how opportunities are progressing</p></div><a class="subtle-link" href="{{ route('leads.index') }}">All leads <x-icon name="arrow-right" size="15" /></a></div>
            <div class="pipeline-list">
                @foreach ($pipeline as $stage)
                    @php $stageClass = in_array($stage['key'], ['converted','lost'], true) ? $stage['key'] : ($stage['key'] === 'new' ? 'new' : 'active'); @endphp
                    <a class="pipeline-row" href="{{ route('leads.index', ['status' => $stage['key']]) }}">
                        <span class="pipeline-stage"><span class="pipeline-stage-dot stage-{{ $stageClass }}"></span>{{ $stage['label'] }}</span>
                        <span class="pipeline-bar"><i class="bar-{{ $stageClass }}" style="width:{{ $stage['percent'] }}%"></i></span>
                        <strong>{{ $stage['count'] }}</strong>
                    </a>
                @endforeach
            </div>
            <div class="pipeline-caption"><span><i class="legend-dot legend-orange"></i> Active opportunities</span><span>{{ number_format($total) }} total</span></div>
        </section>

        <section class="surface-card activity-card">
            <div class="card-heading"><div><h2>Lead activity</h2><p>New leads added over the last 7 days</p></div><span class="chart-total"><strong>{{ $weeklyTrend->sum('count') }}</strong><small>this week</small></span></div>
            <div class="chart-wrap" role="img" aria-label="Bar chart showing new leads each day for the past seven days">
                <div class="chart-y-labels"><span>{{ max(1, $weeklyTrend->max('count')) }}</span><span>{{ max(1, (int) ceil($weeklyTrend->max('count') / 2)) }}</span><span>0</span></div>
                <div class="bar-chart">
                    @foreach ($weeklyTrend as $day)
                        @php $height = $weeklyTrend->max('count') > 0 ? max(10, ($day['count'] / $weeklyTrend->max('count')) * 100) : 8; @endphp
                        <div class="bar-column"><div class="bar-tooltip">{{ $day['count'] }} {{ $day['count'] === 1 ? 'lead' : 'leads' }}</div><i class="chart-bar {{ $day['date'] === $today->toDateString() ? 'chart-bar-current' : '' }}" style="height:{{ $height }}%"></i><span>{{ $day['label'] }}</span></div>
                    @endforeach
                </div>
            </div>
            <div class="chart-footer"><span class="chart-legend"><i></i> Leads added</span><span class="chart-period">Last 7 days</span></div>
        </section>
    </div>

    <div class="dashboard-grid dashboard-grid-secondary">
        <section class="surface-card followup-card">
            <div class="card-heading"><div><h2>Today & coming up</h2><p>Your next conversations, all in one place</p></div><a class="subtle-link" href="{{ route('follow-ups.index') }}">View all <x-icon name="arrow-right" size="15" /></a></div>
            @if ($upcoming->isEmpty())
                <div class="empty-state empty-state-compact"><span class="empty-icon"><x-icon name="check-circle" size="24" /></span><strong>Nothing on the calendar</strong><p>When you schedule a follow-up, it’ll show up here.</p><a class="btn btn-secondary btn-sm" href="{{ route('follow-ups.create') }}"><x-icon name="plus" size="15" /> Schedule one</a></div>
            @else
                <div class="upcoming-list">
                    @foreach ($upcoming as $item)
                        <a class="upcoming-item" href="{{ route('leads.show', $item->lead) }}">
                            <span class="upcoming-date {{ $item->due_at->isToday() ? 'upcoming-today' : '' }}"><strong>{{ $item->due_at->format('d') }}</strong><small>{{ $item->due_at->format('M') }}</small></span>
                            <span class="upcoming-details"><strong>{{ $item->lead->name }}</strong><small>{{ $item->lead->gym_name }} <span class="mini-sep">·</span> {{ crm_label($item->kind) }}</small></span>
                            <span class="upcoming-time">{{ $item->due_at->format('g:i A') }}</span>
                            <x-icon name="chevron-right" size="16" class="upcoming-chevron" />
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="surface-card recent-card">
            <div class="card-heading"><div><h2>Recent leads</h2><p>Latest additions to your workspace</p></div><a class="subtle-link" href="{{ route('leads.index') }}">See all <x-icon name="arrow-right" size="15" /></a></div>
            @if ($recentLeads->isEmpty())
                <div class="empty-state empty-state-compact"><span class="empty-icon"><x-icon name="users" size="23" /></span><strong>Your pipeline starts here</strong><p>Add your first gym lead and keep the momentum going.</p><a class="btn btn-primary btn-sm" href="{{ route('leads.create') }}" data-lead-form-url="{{ route('leads.create') }}"><x-icon name="plus" size="15" /> Add a lead</a></div>
            @else
                <div class="recent-list">
                    @foreach ($recentLeads as $lead)
                        <a href="{{ route('leads.show', $lead) }}" class="recent-item">
                            <span class="avatar avatar-soft avatar-{{ ['orange','blue','green','purple','rose'][$loop->index % 5] }}">{{ collect(explode(' ', $lead->name))->take(2)->map(fn($piece) => mb_substr($piece, 0, 1))->implode('') }}</span>
                            <span class="recent-main"><strong>{{ $lead->name }}</strong><small>{{ $lead->gym_name }}</small></span>
                            <span class="priority-pill priority-{{ $lead->priority }}"><i></i>{{ ucfirst($lead->priority) }}</span>
                            <span class="recent-date">{{ $lead->created_at->diffForHumans(null, true) }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
