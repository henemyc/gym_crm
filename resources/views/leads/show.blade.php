@extends('layouts.app')
@section('title', $lead->name)
@section('page-name', 'Lead details')

@section('content')
    <div data-lead-profile data-lead-index-url="{{ route('leads.index') }}">
    <div class="breadcrumb"><a href="{{ route('leads.index') }}">Leads</a><x-icon name="chevron-right" size="13" /><span>{{ $lead->name }}</span></div>
    <div class="page-heading detail-heading">
        <div><div class="eyebrow"><span class="eyebrow-dot"></span> LEAD PROFILE <span class="heading-date">Added {{ $lead->created_at->format('j M Y') }}</span></div><div class="detail-title-line"><h1>{{ $lead->name }}</h1><span class="priority-pill priority-{{ $lead->priority }}"><i></i>{{ ucfirst($lead->priority) }} priority</span></div><p class="detail-subline"><x-icon name="building" size="15" /> {{ $lead->gym_name }} <span class="mini-sep">·</span><x-icon name="map-pin" size="14" /> {{ $lead->location ?: 'Location not set' }}</p></div>
        <div class="detail-actions">
            @can('update', $lead)<a class="btn btn-secondary" href="{{ route('leads.edit', $lead) }}" data-lead-form-url="{{ route('leads.edit', $lead) }}"><x-icon name="edit" size="15" /> Edit lead</a>@endcan
            <a class="btn btn-primary" href="{{ route('follow-ups.create', ['lead_id' => $lead->id]) }}"><x-icon name="calendar-plus" size="16" /> Add follow-up</a>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-main-stack">
            <section class="surface-card detail-profile">
                <div class="detail-profile-head"><span class="avatar avatar-soft avatar-orange">{{ collect(explode(' ', $lead->name))->take(2)->map(fn($piece) => mb_substr($piece, 0, 1))->implode('') }}</span><div class="detail-profile-copy"><h2>{{ $lead->gym_name }}</h2><p>{{ $lead->source }} lead <span class="mini-sep">·</span> in pipeline {{ $lead->created_at->diffForHumans(null, true) }}</p></div><span class="status-pill status-{{ str_replace('_', '-', $lead->status) }}" data-lead-status-pill>{{ \App\Models\Lead::STATUSES[$lead->status] ?? crm_label($lead->status) }}</span></div>
                <div class="detail-profile-meta">
                    <div class="detail-meta-item"><span>Phone number</span><a href="tel:{{ preg_replace('/[^0-9+]/', '', $lead->phone) }}">{{ $lead->phone }}</a></div>
                    <div class="detail-meta-item"><span>Location</span><strong>{{ $lead->location ?: 'Not provided' }}</strong></div>
                    <div class="detail-meta-item"><span>Lead source</span><strong>{{ $lead->source }}</strong></div>
                    <div class="detail-meta-item"><span>Assigned to</span><strong>{{ $lead->assignee?->name ?? 'Unassigned' }}</strong></div>
                </div>
            </section>

            <section class="surface-card detail-panel">
                <div class="card-heading"><div><h2>Activity timeline</h2><p>Every touchpoint, note, and handoff.</p></div><span class="status-pill status-orange">{{ $activities->count() }} events</span></div>
                @if ($activities->isEmpty())
                    <div class="empty-state empty-state-compact"><span class="empty-icon"><x-icon name="activity" size="23" /></span><strong>Your first touchpoint goes here</strong><p>Add a note or log a call to build the conversation history.</p></div>
                @else
                    <div class="timeline">
                        @foreach ($activities as $activity)
                            @php $eventColor = match ($activity->event) { 'follow_up_completed', 'converted' => 'event-green', 'status_changed', 'lead_created' => 'event-blue', 'meeting', 'trial' => 'event-purple', default => '' }; $eventIcon = match ($activity->event) { 'call' => 'phone', 'whatsapp' => 'message-circle', 'note' => 'file', 'follow_up_completed' => 'check', 'status_changed' => 'activity', 'meeting' => 'calendar', default => 'spark' }; @endphp
                            <article class="timeline-item">
                                <span class="timeline-dot {{ $eventColor }}"><x-icon name="{{ $eventIcon }}" size="10" /></span>
                                <div class="timeline-item-head"><strong>{{ $activity->title }}</strong><time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->format('j M · g:i A') }}</time></div>
                                @if ($activity->description)<p>{{ $activity->description }}</p>@endif
                                @if ($activity->user)<div class="timeline-author">by {{ $activity->user->name }}</div>@endif
                            </article>
                        @endforeach
                    </div>
                @endif
                <form class="note-form" method="POST" action="{{ route('leads.activities.store', $lead) }}">
                    @csrf<input type="hidden" name="event" value="note"><label class="field-label" for="activity-note">Add a note</label><textarea class="textarea-control" id="activity-note" name="description" maxlength="5000" placeholder="Capture a useful detail from your latest conversation..." required></textarea>@error('description')<span class="field-error">{{ $message }}</span>@enderror<button class="btn btn-secondary btn-sm" type="submit"><x-icon name="plus" size="14" /> Add to timeline</button>
                </form>
            </section>

            <section class="surface-card detail-panel">
                <div class="card-heading"><div><h2>Lead notes</h2><p>Helpful context for the next person who picks this up.</p></div>@can('update', $lead)<a class="subtle-link" href="{{ route('leads.edit', $lead) }}" data-lead-form-url="{{ route('leads.edit', $lead) }}"><x-icon name="edit" size="14" /> Edit</a>@endcan</div>
                @if ($lead->notes)<div class="notes-content">{{ $lead->notes }}</div>@else<div class="notes-content table-muted">No lead notes yet. Add context while it’s fresh.</div>@endif
            </section>
        </div>

        <aside class="detail-side-stack">
            <section class="surface-card detail-panel">
                <div class="card-heading"><div><h2>Quick actions</h2><p>Keep the conversation moving</p></div></div>
                <div class="action-grid">
                    <a class="action-tile" href="tel:{{ preg_replace('/[^0-9+]/', '', $lead->phone) }}" data-contact-event="call" data-activity-url="{{ route('leads.activities.store', $lead) }}"><span class="action-tile-icon"><x-icon name="phone" size="16" /></span><span><strong>Call lead</strong><small>{{ $lead->phone }}</small></span></a>
                    <a class="action-tile" href="https://wa.me/{{ preg_replace('/\D/', '', $lead->phone) }}" target="_blank" rel="noopener noreferrer" data-contact-event="whatsapp" data-activity-url="{{ route('leads.activities.store', $lead) }}"><span class="action-tile-icon whatsapp"><x-icon name="message-circle" size="16" /></span><span><strong>WhatsApp</strong><small>Open chat</small></span></a>
                </div>
            </section>

            <section class="surface-card detail-panel">
                <div class="card-heading"><div><h2>Pipeline status</h2><p>Move this lead to its next stage</p></div></div>
                @can('update', $lead)<form class="status-form" method="POST" action="{{ route('leads.status', $lead) }}" data-ajax-lead-status>@csrf @method('PATCH')<select class="select-control" name="status" aria-label="Change lead status">@foreach (\App\Models\Lead::STATUSES as $key => $label)<option value="{{ $key }}" @selected($lead->status === $key)>{{ $label }}</option>@endforeach</select><button class="btn btn-primary btn-sm" type="submit">Update</button></form>@else<span class="status-pill status-{{ str_replace('_', '-', $lead->status) }}">{{ \App\Models\Lead::STATUSES[$lead->status] ?? crm_label($lead->status) }}</span>@endcan
            </section>

            <section class="surface-card detail-panel">
                <div class="card-heading"><div><h2>Follow-up history</h2><p>{{ $followUps->count() }} touchpoints scheduled</p></div><a class="subtle-link" href="{{ route('follow-ups.create', ['lead_id' => $lead->id]) }}" title="Add follow-up"><x-icon name="plus" size="16" /></a></div>
                @forelse ($followUps->take(4) as $followUp)
                    <div class="followup-mini"><span class="followup-mini-date"><strong>{{ $followUp->due_at->format('d') }}</strong><small>{{ $followUp->due_at->format('M') }}</small></span><div class="followup-mini-copy"><strong>{{ \App\Models\FollowUp::KINDS[$followUp->kind] ?? crm_label($followUp->kind) }} · {{ $followUp->due_at->format('g:i A') }}</strong><small>{{ $followUp->completed_at ? crm_label($followUp->outcome).' · completed' : ($followUp->due_at->isPast() ? 'Overdue' : 'Upcoming') }}@if($followUp->meeting_with) · with {{ $followUp->meeting_with }}@endif</small></div><span class="status-pill {{ $followUp->completed_at ? 'status-converted' : 'status-orange' }}">{{ $followUp->completed_at ? 'Done' : 'Open' }}</span></div>
                @empty
                    <div class="empty-state empty-state-compact"><span class="empty-icon"><x-icon name="calendar-plus" size="22" /></span><strong>No follow-ups yet</strong><p>Set a time to reconnect with this lead.</p><a class="btn btn-secondary btn-sm" href="{{ route('follow-ups.create', ['lead_id' => $lead->id]) }}"><x-icon name="plus" size="14" /> Schedule follow-up</a></div>
                @endforelse
            </section>

            <section class="surface-card detail-panel">
                <div class="card-heading"><div><h2>Lead ownership</h2><p>Who added and manages this record</p></div></div>
                <div class="followup-mini"><span class="avatar avatar-soft avatar-blue">{{ $lead->assignee?->initials() ?? '?' }}</span><div class="followup-mini-copy"><strong>{{ $lead->assignee?->name ?? 'Unassigned' }}</strong><small>Current owner</small></div></div>
                <div class="followup-mini"><span class="avatar avatar-soft avatar-purple">{{ $lead->creator?->initials() ?? '?' }}</span><div class="followup-mini-copy"><strong>{{ $lead->creator?->name ?? 'System import' }}</strong><small>Added by {{ $lead->created_at->format('j M Y') }}</small></div></div>
            </section>

            @can('delete', $lead)
                <form class="delete-lead-form" method="POST" action="{{ route('leads.destroy', $lead) }}" data-ajax-lead-delete data-lead-delete-name="{{ $lead->name }}">@csrf @method('DELETE')<button class="btn btn-quiet btn-sm" type="submit"><x-icon name="trash" size="14" /> Delete lead</button></form>
            @endcan
        </aside>
    </div>
    </div>
@endsection
