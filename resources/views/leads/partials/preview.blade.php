<div class="lead-dialog-header">
    <div class="lead-dialog-heading-mark"><x-icon name="user" size="19" /></div>
    <div><span class="eyebrow">LEAD PREVIEW</span><h2 id="lead-dialog-title">Lead details</h2><p>Quick view of this opportunity.</p></div>
    <button class="icon-button" type="button" data-lead-dialog-close aria-label="Close dialog"><x-icon name="x" size="19" /></button>
</div>
<div class="lead-preview-body">
    <div class="lead-preview-hero">
        <span class="avatar avatar-soft avatar-orange">{{ collect(explode(' ', $lead->name))->take(2)->map(fn($piece) => mb_substr($piece, 0, 1))->implode('') }}</span>
        <div class="lead-preview-title"><h3>{{ $lead->name }}</h3><p>{{ $lead->gym_name }} <span class="mini-sep">·</span> {{ $lead->location ?: 'Location not set' }}</p></div>
        <span class="status-pill status-{{ str_replace('_', '-', $lead->status) }}">{{ \App\Models\Lead::STATUSES[$lead->status] ?? crm_label($lead->status) }}</span>
    </div>
    <div class="lead-preview-facts">
        <div><span>Phone</span><a href="tel:{{ preg_replace('/[^0-9+]/', '', $lead->phone) }}">{{ $lead->phone }}</a></div>
        <div><span>Source</span><strong>{{ $lead->source }}</strong></div>
        <div><span>Priority</span><strong><span class="priority-pill priority-{{ $lead->priority }}"><i></i>{{ ucfirst($lead->priority) }}</span></strong></div>
        <div><span>Assigned to</span><strong>{{ $lead->assignee?->name ?? 'Unassigned' }}</strong></div>
        <div><span>Added</span><strong>{{ $lead->created_at->format('j M Y') }}</strong></div>
    </div>
    @if ($lead->notes)
        <div class="lead-preview-notes"><span>Notes</span><p>{{ $lead->notes }}</p></div>
    @endif
</div>
<div class="lead-dialog-actions">
    <a class="btn btn-secondary" href="https://wa.me/{{ preg_replace('/\D/', '', $lead->phone) }}" target="_blank" rel="noopener noreferrer"><x-icon name="message-circle" size="16" /> WhatsApp</a>
    @can('update', $lead)<a class="btn btn-secondary" href="{{ route('leads.edit', $lead) }}" data-lead-form-url="{{ route('leads.edit', $lead) }}"><x-icon name="edit" size="16" /> Edit</a>@endcan
    <a class="btn btn-primary" href="{{ route('leads.show', $lead) }}">Open full profile <x-icon name="arrow-right" size="16" /></a>
</div>
