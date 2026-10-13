@extends('layouts.app')
@section('title', 'Prospecting')
@section('page-name', 'Prospecting')

@section('content')
    <div class="breadcrumb"><a href="{{ route('leads.index') }}">Leads</a><x-icon name="chevron-right" size="13" /><span>Prospecting</span></div>
    <div class="page-heading place-page-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-dot"></span> GOOGLE PLACES · LIVE DISCOVERY</div>
            <h1>Gym prospecting</h1>
            <p class="page-description">Find gyms by territory, review live business details, and route prospects to the right teammate.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('leads.index') }}"><x-icon name="users" size="15" /> Open CRM leads</a>
    </div>

    <div class="place-summary-grid" aria-label="Prospect summary">
        <article class="place-summary-card"><span class="place-summary-icon"><x-icon name="map-pin" size="17" /></span><div><small>Assigned prospects</small><strong data-place-stat="total">{{ number_format($summary['total']) }}</strong></div></article>
        <article class="place-summary-card"><span class="place-summary-icon place-summary-icon-active"><x-icon name="activity" size="17" /></span><div><small>In progress</small><strong data-place-stat="active">{{ number_format($summary['active']) }}</strong></div></article>
        <article class="place-summary-card"><span class="place-summary-icon place-summary-icon-converted"><x-icon name="check-circle" size="17" /></span><div><small>Converted to leads</small><strong data-place-stat="converted">{{ number_format($summary['converted']) }}</strong></div></article>
    </div>

    @if($canSearch)
        <section class="surface-card place-search-card" data-place-search-panel>
            <div class="card-heading place-search-heading">
                <div><h2>Search nearby gyms</h2><p>Set a centre and radius. Each page returns up to 20 Google-ranked results.</p></div>
                <span class="place-search-icon"><x-icon name="search" size="17" /></span>
            </div>
            @unless($googlePlacesConfigured)
                <div class="place-api-notice place-api-notice-warning"><x-icon name="alert" size="16" /><span>Google Places is not configured yet. An Admin can add and test the server-side API key in <a href="{{ route('settings.index') }}">Settings</a>.</span></div>
            @endunless
            <form class="place-search-form" method="POST" action="{{ route('prospecting.search') }}" data-place-search-form data-no-loading>
                @csrf
                <div class="form-field place-location-field"><label class="field-label" for="place-search-location">Location</label><input class="form-control" id="place-search-location" name="location" maxlength="120" placeholder="e.g. Gomti Nagar, Lucknow" autocomplete="off" required><span class="field-hint">Use a city, neighbourhood, or full address.</span></div>
                <div class="form-field place-radius-field"><label class="field-label" for="place-search-radius">Radius</label><div class="place-radius-input"><input class="form-control" id="place-search-radius" type="number" name="radius_km" min="1" max="50" step="1" value="{{ config('google_places.radius_default_km', 5) }}" required><span>km</span></div><span class="field-hint">1–50 km maximum.</span></div>
                <button class="btn btn-primary place-search-submit" type="submit" data-place-search-submit @disabled(! $googlePlacesConfigured)><x-icon name="search" size="15" /><span data-place-search-submit-label>Search gyms</span></button>
            </form>
            <div class="place-search-feedback" data-place-search-feedback role="status" aria-live="polite" hidden><span class="place-progress-spinner" data-place-search-spinner aria-hidden="true" hidden><span></span></span><span data-place-search-message>Searching Google Places…</span><span class="place-search-progress" data-place-search-progress aria-hidden="true" hidden><span></span></span></div>
        </section>

        <section class="surface-card place-results-card" data-place-search-results hidden>
            <div class="card-heading place-results-heading">
                <div><h2>Search results</h2><p data-place-search-summary></p></div>
                <span class="place-result-count" data-place-result-count>0 gyms</span>
            </div>
            <div class="place-results-notice"><x-icon name="help" size="15" /><span>Search uses Pro-tier name, address, and Maps fields only. Phone and review count are fetched later by an explicit, metered Enterprise Details request. Google ranks results by relevance, distance, and prominence; this is not an exhaustive business directory. Up to 40 results can be requested in two explicit pages.</span></div>
            <form method="POST" action="{{ route('prospecting.assign') }}" data-place-assign-form data-no-loading>
                @csrf
                <div class="place-results-toolbar">
                    <label class="place-select-all"><input type="checkbox" data-place-select-all disabled> Select loaded results</label>
                    <span class="place-selection-count" data-place-selection-count>0 selected</span>
                    <div class="place-assignment-controls">
                        <label class="sr-only" for="place-assign-to">Assign selected prospects to</label>
                        <select class="select-control" id="place-assign-to" name="assigned_to" required>
                            <option value="">Assign to teammate</option>
                            @foreach($team as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} · {{ \App\Models\User::ROLES[$member->role] ?? 'Team' }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary" type="submit" data-place-assign-submit disabled><x-icon name="users" size="15" /><span>Assign selected</span></button>
                    </div>
                </div>
                <div class="place-results-table-wrap">
                    <table class="place-results-table">
                        <thead><tr><th scope="col">Select</th><th scope="col">Gym</th><th scope="col">Address</th><th scope="col">Google Maps</th><th scope="col">CRM status</th></tr></thead>
                        <tbody data-place-search-rows></tbody>
                    </table>
                </div>
            </form>
            <template data-place-result-template>
                <tr data-place-result-row>
                    <td><input type="checkbox" name="candidates[]" data-place-select aria-label="Select this gym"></td>
                    <td><strong data-place-name></strong></td>
                    <td><span data-place-address></span></td>
                    <td><a class="place-map-link" data-place-map-link target="_blank" rel="noopener noreferrer"><x-icon name="external" size="13" /> Open map</a></td>
                    <td><span class="place-result-status" data-place-result-status></span></td>
                </tr>
            </template>
            <div class="place-results-footer">
                <span class="google-maps-attribution" translate="no">Google Maps</span>
                <span class="place-live-only-label">Live results · not saved to GymXBook</span>
                <button class="btn btn-secondary" type="button" data-place-more disabled><span data-place-more-label>Search more results</span></button>
            </div>
        </section>
    @else
        <div class="place-api-notice place-api-notice-neutral"><x-icon name="shield" size="16" /><span>Only prospects assigned to you are shown. Admins and Managers run Google searches and manage assignments.</span></div>
    @endif

    <section class="surface-card place-prospect-queue" data-place-queue data-queue-url="{{ route('prospecting.index') }}">
        <div class="card-heading place-queue-heading">
            <div><h2>{{ $canSearch ? 'Assigned Google Maps prospects' : 'Your assigned Google Maps prospects' }}</h2><p>GymXBook stores only Google Place IDs and internal ownership/status. “Load live details” makes a separate metered Enterprise request for current phone and review count; those Google fields are not saved.</p></div>
            <span class="status-pill status-orange">{{ number_format($summary['total']) }} assigned</span>
        </div>
        <div class="place-queue-table-wrap">
            <table class="place-prospect-table">
                <thead><tr><th scope="col">Place</th><th scope="col">Assigned to</th><th scope="col">Status</th><th scope="col">Added</th><th scope="col">Actions</th></tr></thead>
                <tbody data-place-prospect-rows>
                    @include('prospecting.partials.prospect-rows', ['prospects' => $prospects, 'canManageTeam' => $canSearch, 'team' => $team])
                </tbody>
            </table>
        </div>
        <div data-place-pagination-wrap>
            @include('prospecting.partials.prospect-pagination', ['prospects' => $prospects])
        </div>
    </section>
@endsection
