@forelse ($leads as $lead)
    @php $avatarColors = ['orange','blue','green','purple','rose']; $avatarColor = $avatarColors[($lead->id - 1) % count($avatarColors)]; @endphp
    <tr data-lead-row data-lead-id="{{ $lead->id }}">
        <td class="check-cell">@can('delete', $lead)<input class="row-checkbox" type="checkbox" value="{{ $lead->id }}" data-row-select aria-label="Select {{ $lead->name }}">@else<span class="row-check-empty" aria-label="Selection unavailable"></span>@endcan</td>
        <td><div class="lead-cell"><span class="avatar avatar-soft avatar-{{ $avatarColor }}">{{ collect(explode(' ', $lead->name))->take(2)->map(fn($piece) => mb_substr($piece, 0, 1))->implode('') }}</span><div class="lead-cell-text"><a href="{{ route('leads.show', $lead) }}" data-lead-preview-url="{{ route('leads.show', $lead) }}">{{ $lead->name }}</a><small>Lead #{{ str_pad((string) $lead->id, 4, '0', STR_PAD_LEFT) }}</small></div></div></td>
        <td><div class="gym-cell"><strong>{{ $lead->gym_name }}</strong><small>{{ $lead->location ?: 'Location not set' }}</small></div></td>
        <td><a class="phone-link" href="tel:{{ preg_replace('/[^0-9+]/', '', $lead->phone) }}">{{ $lead->phone }}</a></td>
        <td>@if($lead->source === 'Google Maps')<span class="place-source-badge place-source-table-tag">Google Maps</span>@else<span class="table-muted">{{ $lead->source }}</span>@endif</td>
        <td><span class="priority-pill priority-{{ $lead->priority }}"><i></i>{{ ucfirst($lead->priority) }}</span></td>
        <td><span class="status-pill status-{{ str_replace('_', '-', $lead->status) }}">{{ \App\Models\Lead::STATUSES[$lead->status] ?? crm_label($lead->status) }}</span></td>
        <td><span class="assignee-cell"><span class="avatar avatar-soft">{{ $lead->assignee?->initials() ?? '?' }}</span><span>{{ $lead->assignee?->name ?? 'Unassigned' }}</span></span></td>
        <td class="table-muted">{{ $lead->created_at->format('d M Y') }}</td>
        <td><span class="row-actions">
            <a class="row-action" href="{{ route('leads.show', $lead) }}" data-lead-preview-url="{{ route('leads.show', $lead) }}" title="Preview lead" aria-label="Preview {{ $lead->name }}"><x-icon name="external" size="15" /></a>
            @can('update', $lead)<a class="row-action" href="{{ route('leads.edit', $lead) }}" data-lead-form-url="{{ route('leads.edit', $lead) }}" title="Edit lead" aria-label="Edit {{ $lead->name }}"><x-icon name="edit" size="15" /></a>@endcan
            @can('delete', $lead)<form class="inline-row-form" method="POST" action="{{ route('leads.destroy', $lead) }}" data-ajax-lead-delete data-lead-delete-name="{{ $lead->name }}">@csrf @method('DELETE')<button class="row-action danger" type="submit" title="Delete lead" aria-label="Delete {{ $lead->name }}"><x-icon name="trash" size="15" /></button></form>@endcan
        </span></td>
    </tr>
@empty
    <tr><td colspan="10"><div class="empty-state"><span class="empty-icon"><x-icon name="users" size="24" /></span><strong>{{ $activeFilters->isNotEmpty() ? 'No leads match those filters' : 'Your pipeline starts here' }}</strong><p>{{ $activeFilters->isNotEmpty() ? 'Try a different search or clear a filter to see more leads.' : 'Add your first gym lead or import a spreadsheet to build your pipeline.' }}</p>@if ($activeFilters->isNotEmpty())<button class="btn btn-secondary btn-sm" type="button" data-clear-lead-filters>Clear filters</button>@else<a class="btn btn-primary btn-sm" href="{{ route('leads.create') }}" data-lead-form-url="{{ route('leads.create') }}"><x-icon name="plus" size="15" /> Add your first lead</a>@endif</div></td></tr>
@endforelse
