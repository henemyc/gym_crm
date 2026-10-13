@extends('layouts.app')
@section('title', 'Reports')
@section('page-name', 'Reports')

@section('content')
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> BETTER FOLLOW-THROUGH, BY THE NUMBERS</div><h1>Reports</h1><p class="page-description">A clear view of what’s bringing gyms into your pipeline and moving them forward.</p></div><form method="GET" action="{{ route('reports.index') }}"><select class="filter-select" name="period" onchange="this.form.submit()" aria-label="Report date range"><option value="30" @selected($period === 30)>Last 30 days</option><option value="90" @selected($period === 90)>Last 90 days</option><option value="365" @selected($period === 365)>Last 12 months</option></select></form></div>
    <div class="report-kpi-grid">
        <article class="report-kpi"><div class="report-kpi-top"><span>Leads created</span><span class="metric-icon metric-icon-orange"><x-icon name="users" size="16" /></span></div><strong>{{ number_format($total) }}</strong><small>in the selected period</small></article>
        <article class="report-kpi"><div class="report-kpi-top"><span>Conversion rate</span><span class="metric-icon metric-icon-green"><x-icon name="target" size="16" /></span></div><strong>{{ $conversionRate }}%</strong><small>{{ number_format($converted) }} converted leads</small></article>
        <article class="report-kpi"><div class="report-kpi-top"><span>Follow-ups completed</span><span class="metric-icon metric-icon-blue"><x-icon name="check-circle" size="16" /></span></div><strong>{{ number_format($followUpsCompleted) }}</strong><small>{{ $followUpRate }}% completion rate</small></article>
        <article class="report-kpi"><div class="report-kpi-top"><span>Meetings & trials</span><span class="metric-icon metric-icon-purple"><x-icon name="calendar" size="16" /></span></div><strong>{{ number_format($meetings + $trials) }}</strong><small>{{ $meetings }} meetings · {{ $trials }} trials</small></article>
    </div>

    <div class="report-chart-grid">
        <section class="surface-card report-card"><div class="card-heading"><div><h2>Leads by source</h2><p>Where your opportunities begin</p></div><span class="status-pill status-orange">{{ number_format($total) }} leads</span></div>
            @if($sourceRows->isEmpty())<div class="report-empty">Source data will appear when leads are added.</div>@else<div class="report-bars">@foreach($sourceRows as $row)<div class="report-bar-row"><span>{{ $row['label'] }}</span><span class="report-bar-track"><i class="report-bar-fill" style="width:{{ $sourceMax ? ($row['count']/$sourceMax)*100 : 0 }}%"></i></span><strong>{{ $row['count'] }}</strong></div>@endforeach</div>@endif
            <div class="report-card-foot"><span>Source attributed at creation</span><span>Selected date range</span></div>
        </section>
        <section class="surface-card report-card"><div class="card-heading"><div><h2>Leads by status</h2><p>Movement through your sales pipeline</p></div><x-icon name="activity" size="18" class="text-muted" /></div>
            @if($statusRows->isEmpty())<div class="report-empty">Pipeline data will appear when leads are added.</div>@else<div class="report-bars">@foreach($statusRows as $row)<div class="report-bar-row"><span>{{ $row['label'] }}</span><span class="report-bar-track"><i class="report-bar-fill fill-blue" style="width:{{ $statusMax ? ($row['count']/$statusMax)*100 : 0 }}%"></i></span><strong>{{ $row['count'] }}</strong></div>@endforeach</div>@endif
            <div class="report-card-foot"><span>{{ number_format($activeLeads) }} active opportunities</span><span>Converted + lost included</span></div>
        </section>
        <section class="surface-card report-card"><div class="card-heading"><div><h2>Leads by priority</h2><p>Focus your team’s attention wisely</p></div><x-icon name="flame" size="18" class="text-muted" /></div>
            <div class="report-bars">@foreach($priorityRows as $row)<div class="report-bar-row"><span>{{ $row['label'] }}</span><span class="report-bar-track"><i class="report-bar-fill {{ $row['key'] === 'hot' ? '' : ($row['key'] === 'warm' ? 'fill-yellow' : 'fill-blue') }}" style="width:{{ $priorityMax ? ($row['count']/$priorityMax)*100 : 0 }}%"></i></span><strong>{{ $row['count'] }}</strong></div>@endforeach</div>
            <div class="report-card-foot"><span>{{ number_format($total) }} leads qualified</span><span>Hot · Warm · Cold</span></div>
        </section>
        <section class="surface-card report-card"><div class="card-heading"><div><h2>Conversion rate</h2><p>Closed-won share of created leads</p></div><x-icon name="target" size="18" class="text-muted" /></div>
            <div class="conversion-panel"><div class="conversion-ring" style="--conversion: {{ min(100, $conversionRate) }}%"><span>{{ $conversionRate }}%</span></div><div class="conversion-copy"><h3>{{ $converted }} gyms joined</h3><p>from {{ number_format($total) }} leads created over the last {{ $period }} days.</p><div class="conversion-legend"><span><i></i> Converted <strong>{{ number_format($converted) }}</strong></span><span><i class="legend-gray"></i> Not converted <strong>{{ number_format(max(0, $total - $converted)) }}</strong></span></div></div></div>
            <div class="report-card-foot"><span>Conversion is measured by lead status</span><span>Keep nurturing the rest</span></div>
        </section>
        <section class="surface-card report-card"><div class="card-heading"><div><h2>Follow-up performance</h2><p>Reliability of your team’s touchpoints</p></div><x-icon name="clock" size="18" class="text-muted" /></div>
            <div class="report-bars"><div class="report-bar-row"><span>Completed</span><span class="report-bar-track"><i class="report-bar-fill fill-green" style="width:{{ $followUpTotal ? ($followUpsCompleted/$followUpTotal)*100 : 0 }}%"></i></span><strong>{{ $followUpsCompleted }}</strong></div><div class="report-bar-row"><span>Open</span><span class="report-bar-track"><i class="report-bar-fill fill-yellow" style="width:{{ $followUpTotal ? ($followUpsOpen/$followUpTotal)*100 : 0 }}%"></i></span><strong>{{ $followUpsOpen }}</strong></div><div class="report-bar-row"><span>Overdue</span><span class="report-bar-track"><i class="report-bar-fill" style="background:#e07171;width:{{ $followUpTotal ? ($followUpsOverdue/$followUpTotal)*100 : 0 }}%"></i></span><strong>{{ $followUpsOverdue }}</strong></div></div>
            <div class="report-card-foot"><span>{{ number_format($followUpTotal) }} touchpoints scheduled</span><span>{{ $followUpRate }}% completed</span></div>
        </section>
        <section class="surface-card report-card"><div class="card-heading"><div><h2>Meetings & trials</h2><p>Product conversations booked in this period</p></div><x-icon name="calendar" size="18" class="text-muted" /></div>
            <div class="conversion-panel"><div class="event-stat-block"><span class="metric-icon metric-icon-purple"><x-icon name="calendar" size="19" /></span><strong>{{ $meetings }}</strong><small>Meetings</small></div><span class="event-stat-divider"></span><div class="event-stat-block"><span class="metric-icon metric-icon-green"><x-icon name="target" size="19" /></span><strong>{{ $trials }}</strong><small>Trials</small></div></div>
            <div class="report-card-foot"><span>Event dates in selected period</span><a class="subtle-link" href="{{ route('calendar.index') }}">Open calendar <x-icon name="arrow-right" size="13" /></a></div>
        </section>
    </div>
@endsection
