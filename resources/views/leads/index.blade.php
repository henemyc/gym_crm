@extends('layouts.app')
@section('title', 'Leads')
@section('page-name', 'Leads')

@section('content')
    <div class="page-heading leads-page-heading">
        <div><div class="eyebrow"><span class="eyebrow-dot"></span> SALES PIPELINE</div><h1>Leads <span class="heading-count" data-total-leads>{{ number_format($leads->total()) }}</span></h1><p class="page-description">Search, qualify, and move every gym opportunity forward.</p></div>
        <div class="heading-actions"><a class="btn btn-secondary" href="{{ route('leads.export', request()->query()) }}" data-lead-export data-lead-export-url="{{ route('leads.export') }}"><x-icon name="download" size="16" /> Export CSV</a><a class="btn btn-primary" href="{{ route('leads.create') }}" data-lead-form-url="{{ route('leads.create') }}"><x-icon name="plus" size="17" /> Add lead</a></div>
    </div>

    <section class="lead-workspace" data-lead-index>
        <form class="leads-filterbar" method="GET" action="{{ route('leads.index') }}" data-lead-filter-form>
            <div class="leads-filterbar-main">
                <label class="lead-search-control"><x-icon name="search" size="17" /><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search leads, gyms or phone..." aria-label="Search leads" autocomplete="off"><span class="lead-search-spinner" data-lead-spinner hidden aria-label="Searching"></span></label>
                <select class="lead-inline-filter" name="status" aria-label="Filter by status"><option value="">All statuses</option>@foreach (\App\Models\Lead::STATUSES as $key => $label)<option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>@endforeach</select>
                <select class="lead-inline-filter" name="priority" aria-label="Filter by priority"><option value="">All priorities</option>@foreach (\App\Models\Lead::PRIORITIES as $key => $label)<option value="{{ $key }}" @selected(($filters['priority'] ?? '') === $key)>{{ $label }}</option>@endforeach</select>
                <button class="btn btn-secondary btn-sm lead-more-filters" type="button" data-filter-panel-toggle aria-expanded="false" aria-controls="lead-advanced-filters"><x-icon name="filter" size="15" /> More filters <span class="filter-count" data-active-filter-count hidden>0</span></button>
                <a class="btn btn-quiet btn-sm lead-reset-filters" href="{{ route('leads.index') }}" data-reset-lead-filters>Reset</a>
                <button class="btn btn-primary btn-sm lead-apply-filters" type="submit"><x-icon name="search" size="14" /> Apply</button>
                <span class="lead-live-count" role="status" aria-live="polite"><span data-lead-result-label>{{ $leads->total() }} {{ \Illuminate\Support\Str::plural('lead', $leads->total()) }}</span></span>
            </div>
            <div class="lead-filter-popover" id="lead-advanced-filters" data-filter-panel hidden>
                <div class="lead-filter-popover-heading"><div><strong>Refine your pipeline</strong><small>These filters update results as you change them.</small></div><button class="icon-button" type="button" data-filter-panel-close aria-label="Close filters"><x-icon name="x" size="17" /></button></div>
                <div class="lead-advanced-grid">
                    <label class="form-field"><span class="field-label">Source</span><select class="filter-select" name="source"><option value="">All sources</option>@foreach ($sources as $source)<option value="{{ $source }}" @selected(($filters['source'] ?? '') === $source)>{{ $source }}</option>@endforeach</select></label>
                    <label class="form-field"><span class="field-label">Location</span><select class="filter-select" name="location"><option value="">All locations</option>@foreach ($locations as $location)<option value="{{ $location }}" @selected(($filters['location'] ?? '') === $location)>{{ $location }}</option>@endforeach</select></label>
                    <label class="form-field"><span class="field-label">Added from</span><input class="filter-date" type="date" name="from" value="{{ $filters['from'] ?? '' }}"></label>
                    <label class="form-field"><span class="field-label">Added to</span><input class="filter-date" type="date" name="to" value="{{ $filters['to'] ?? '' }}"></label>
                    @if (auth()->user()->canManageTeam())<label class="form-field"><span class="field-label">Owner</span><select class="filter-select" name="assigned_to"><option value="">All owners</option>@foreach ($team as $member)<option value="{{ $member->id }}" @selected((string) ($filters['assigned_to'] ?? '') === (string) $member->id)>{{ $member->name }}</option>@endforeach</select></label>@endif
                    <label class="form-field"><span class="field-label">Rows per page</span><select class="filter-select" name="per_page"><option value="15" @selected(($filters['per_page'] ?? '15') == '15')>15 leads</option><option value="30" @selected(($filters['per_page'] ?? '') == '30')>30 leads</option><option value="50" @selected(($filters['per_page'] ?? '') == '50')>50 leads</option></select></label>
                </div>
            </div>
        </form>

        <div class="bulk-toolbar" data-bulk-toolbar hidden>
            <strong><span data-selected-count>0</span> selected</strong><span>Choose an action for these leads.</span><span class="toolbar-spacer"></span>
            <button type="button" class="btn btn-quiet btn-sm" data-clear-selection>Clear selection</button>
            <button type="button" class="btn btn-danger btn-sm" data-bulk-delete-trigger><x-icon name="trash" size="14" /> Delete selected</button>
        </div>
        <form id="bulk-delete-form" method="POST" action="{{ route('leads.bulk-destroy') }}" data-ajax-bulk-delete>@csrf @method('DELETE')<div data-selected-hidden></div></form>

        <section class="table-card lead-table-card" aria-label="Lead results">
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th class="check-cell"><input class="select-all" type="checkbox" data-select-all aria-label="Select all leads on this page"></th><th>Lead</th><th>Gym & location</th><th>Phone</th><th>Source</th><th>Priority</th><th>Status</th><th>Assigned to</th><th>Added</th><th aria-label="Actions"></th></tr></thead>
                    <tbody data-lead-results>@include('leads.partials.rows', ['leads' => $leads, 'activeFilters' => $activeFilters])</tbody>
                </table>
            </div>
            <div data-lead-footer>@include('leads.partials.footer', ['leads' => $leads])</div>
        </section>

        @include('leads.partials.place-prospects')
    </section>

@endsection
