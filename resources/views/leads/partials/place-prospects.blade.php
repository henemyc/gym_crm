@if($showPlaceAssignmentNotice)
    <div class="place-assignment-success" data-place-assignment-notice role="status" aria-live="polite">
        <x-icon name="check-circle" size="17" />
        <span>These Google Maps prospects are now available in Leads for follow-up. Existing assignments are left unchanged when a duplicate is selected.</span>
    </div>
@endif

<section class="surface-card place-prospect-queue lead-place-prospect-section" id="google-maps-prospects" data-place-queue data-queue-url="{{ route('prospecting.index', ['open_only' => 1]) }}" data-queue-page-param="prospect_page" data-open-only="true" aria-labelledby="google-maps-prospects-title">
    <div class="card-heading place-queue-heading">
        <div>
            <div class="place-leads-eyebrow"><span class="place-source-badge">Google Maps</span><span>ASSIGNED PROSPECTS</span></div>
            <h2 id="google-maps-prospects-title">Google Maps prospects</h2>
            <p>Assigned prospects are visible to their teammate here. Google details stay live-only; create a standard lead after independently verifying contact details.</p>
        </div>
        <div class="place-leads-heading-actions">
            <span class="status-pill status-orange" data-place-open-count>{{ number_format($placeProspectSummary['total']) }} open</span>
            @if(auth()->user()->canManageTeam())
                <a class="btn btn-secondary btn-sm" href="{{ route('prospecting.index') }}"><x-icon name="search" size="14" /> Search gyms</a>
            @endif
        </div>
    </div>
    <div class="place-queue-table-wrap">
        <table class="place-prospect-table">
            <thead><tr><th scope="col">Place / source</th><th scope="col">Assigned to</th><th scope="col">Status</th><th scope="col">Added</th><th scope="col">Actions</th></tr></thead>
            <tbody data-place-prospect-rows>
                @include('prospecting.partials.prospect-rows', [
                    'prospects' => $placeProspects,
                    'canManageTeam' => auth()->user()->canManageTeam(),
                    'team' => $team,
                ])
            </tbody>
        </table>
    </div>
    <div data-place-pagination-wrap>
        @include('prospecting.partials.prospect-pagination', ['prospects' => $placeProspects])
    </div>
</section>
