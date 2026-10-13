<tr data-place-prospect-row data-place-prospect-id="{{ $prospect->id }}">
    <td>
        <div class="place-prospect-identity">
            <span class="place-prospect-mark"><x-icon name="map-pin" size="16" /></span>
            <span><strong>Google Place prospect</strong><small><span class="place-source-inline">Google Maps</span> · Place ID {{ substr($prospect->place_id, 0, 7) }}…{{ substr($prospect->place_id, -4) }}</small></span>
        </div>
    </td>
    <td>
        @if($canManageTeam)
            <form class="place-reassign-form" method="POST" action="{{ route('prospecting.reassign', $prospect) }}" data-place-reassign-form data-no-loading>
                @csrf @method('PATCH')
                <select class="select-control" name="assigned_to" aria-label="Assign prospect to teammate">
                    @if($prospect->assignee && ! $prospect->assignee->is_active)
                        <option value="{{ $prospect->assignee->id }}" selected disabled>{{ $prospect->assignee->name }} · inactive</option>
                    @endif
                    @foreach($team as $member)
                        <option value="{{ $member->id }}" @selected((int) $prospect->assigned_to === (int) $member->id)>{{ $member->name }} · {{ \App\Models\User::ROLES[$member->role] ?? 'Team' }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary btn-sm" type="submit" aria-label="Save assignment"><x-icon name="check" size="13" /></button>
            </form>
        @else
            <span class="place-owner-name">{{ $prospect->assignee?->name ?? 'Unassigned' }}</span>
        @endif
    </td>
    <td>
        @if($prospect->isConverted())
            <span class="status-pill status-converted" data-place-prospect-status>Converted to lead</span>
        @else
            <form class="place-status-form" method="POST" action="{{ route('prospecting.status', $prospect) }}" data-place-status-form data-no-loading>
                @csrf @method('PATCH')
                <select class="select-control" name="status" aria-label="Prospect status">
                    @foreach(['new', 'contacted', 'qualified', 'not_interested'] as $status)
                        <option value="{{ $status }}" @selected($prospect->status === $status)>{{ \App\Models\GooglePlaceProspect::STATUSES[$status] }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary btn-sm" type="submit" aria-label="Save prospect status"><x-icon name="check" size="13" /></button>
            </form>
        @endif
    </td>
    <td><span class="place-added-date">{{ $prospect->created_at?->format('d M Y') }}</span></td>
    <td>
        <div class="place-prospect-actions">
            <button class="btn btn-secondary btn-sm" type="button" data-place-details-button data-details-url="{{ route('prospecting.details', $prospect) }}" aria-expanded="false" aria-controls="place-details-panel-{{ $prospect->id }}" aria-label="Load live Google details using one metered Enterprise request"><x-icon name="eye" size="14" /> Load live details</button>
            @if($prospect->isConverted())
                <a class="btn btn-secondary btn-sm" href="{{ route('leads.show', $prospect->lead_id) }}" data-lead-preview-url="{{ route('leads.show', $prospect->lead_id) }}"><x-icon name="briefcase" size="14" /> Open lead</a>
            @else
                <a class="btn btn-primary btn-sm" href="{{ route('prospecting.convert-form', $prospect) }}" data-lead-form-url="{{ route('prospecting.convert-form', $prospect) }}"><x-icon name="plus" size="14" /> Create verified lead</a>
            @endif
        </div>
    </td>
</tr>
<tr class="place-live-details-row" data-place-details-row="{{ $prospect->id }}" hidden>
    <td colspan="5"><div id="place-details-panel-{{ $prospect->id }}" class="place-live-details-content" data-place-details-content="{{ $prospect->id }}" role="region" aria-label="Live Google details for assigned gym prospect" aria-live="polite" aria-busy="false"></div></td>
</tr>
