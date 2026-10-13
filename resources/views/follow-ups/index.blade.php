@extends('layouts.app')
@section('title', 'Follow-ups')
@section('page-name', 'Follow-ups')

@section('content')
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> NO CONVERSATION LEFT BEHIND</div><h1>Follow-ups</h1><p class="page-description">A calm, reliable rhythm for every relationship in your pipeline.</p></div><div class="heading-actions"><a class="btn btn-secondary" href="{{ route('calendar.index') }}"><x-icon name="calendar" size="16" /> Open calendar</a><a class="btn btn-primary" href="{{ route('follow-ups.create') }}"><x-icon name="plus" size="17" /> Schedule touchpoint</a></div></div>
    <div class="toolbar-card followup-tabs-row"><div class="tab-bar" role="tablist" aria-label="Follow-up views">
        @foreach (['today' => 'Today', 'upcoming' => 'Upcoming', 'overdue' => 'Overdue', 'completed' => 'Completed', 'all' => 'All'] as $key => $label)<a class="tab-link {{ $activeView === $key ? 'active' : '' }}" href="{{ route('follow-ups.index', array_merge(request()->except('page'), ['view' => $key])) }}" role="tab" aria-selected="{{ $activeView === $key ? 'true' : 'false' }}">{{ $label }}<span class="tab-count">{{ $counts[$key] }}</span></a>@endforeach
    </div><form class="table-search" method="GET" action="{{ route('follow-ups.index') }}"><x-icon name="search" size="15" /><input type="hidden" name="view" value="{{ $activeView }}"><input type="search" name="q" value="{{ request('q') }}" placeholder="Search lead or gym..." aria-label="Search follow-ups"></form></div>

    @if ($followUps->isEmpty())
        <section class="surface-card"><div class="empty-state"><span class="empty-icon"><x-icon name="{{ $activeView === 'completed' ? 'check-circle' : 'clock' }}" size="24" /></span><strong>{{ $activeView === 'today' ? 'A clear day, a good sign' : 'No touchpoints in this view' }}</strong><p>{{ $activeView === 'today' ? 'There are no follow-ups due today. Check upcoming or schedule your next conversation.' : 'Try another view or create a new touchpoint to keep things moving.' }}</p><a class="btn btn-primary btn-sm" href="{{ route('follow-ups.create') }}"><x-icon name="plus" size="15" /> Schedule touchpoint</a></div></section>
    @else
        <div class="followup-list">
            @foreach ($followUps as $followUp)
                @php $isOverdue = ! $followUp->completed_at && $followUp->due_at->lt($today->copy()->startOfDay()); $accent = $followUp->completed_at ? 'completed' : ($isOverdue ? 'overdue' : ($followUp->kind === 'meeting' ? 'meeting' : ($followUp->kind === 'trial' ? 'trial' : ''))); @endphp
                <article class="followup-row-card">
                    <span class="followup-accent {{ $accent }}"></span>
                    <div class="followup-lead-info"><a href="{{ route('leads.show', $followUp->lead) }}"><strong>{{ $followUp->lead->name }}</strong></a><small>{{ $followUp->lead->gym_name }} <span class="mini-sep">·</span> {{ $followUp->lead->location ?: 'Location not set' }}</small></div>
                    <div class="followup-when"><strong>{{ $followUp->due_at->format('D, j M · g:i A') }}</strong><small>@if($followUp->completed_at)Completed {{ $followUp->completed_at->diffForHumans() }}@elseif($isOverdue)Overdue {{ $followUp->due_at->diffForHumans() }}@elseif($followUp->due_at->isToday())Due today@else{{ $followUp->due_at->diffForHumans() }}@endif <span class="mini-sep">·</span> {{ \App\Models\FollowUp::KINDS[$followUp->kind] ?? crm_label($followUp->kind) }}</small></div>
                    <div class="followup-owner"><span class="avatar avatar-soft avatar-blue">{{ $followUp->assignee?->initials() ?? '?' }}</span><div><strong>{{ $followUp->assignee?->name ?? 'Unassigned' }}</strong><small>Owner</small></div></div>
                    <div class="followup-controls">
                        @if ($followUp->completed_at)
                            <div class="followup-result" title="{{ $followUp->remark }}"><span class="status-pill status-converted">{{ \App\Models\FollowUp::OUTCOMES[$followUp->outcome] ?? 'Completed' }}</span></div>
                            <a class="row-action" href="{{ route('leads.show', $followUp->lead) }}" title="Open lead"><x-icon name="external" size="15" /></a>
                        @else
                            <details class="complete-details"><summary class="btn btn-secondary btn-sm"><x-icon name="check" size="14" /> Complete</summary>
                                <form class="complete-panel" method="POST" action="{{ route('follow-ups.complete', $followUp) }}">@csrf @method('PATCH')
                                    <strong>Log the outcome</strong>
                                    <div class="form-field"><label class="field-label" for="outcome-{{ $followUp->id }}">Outcome</label><select class="select-control" id="outcome-{{ $followUp->id }}" name="outcome" required><option value="">Choose outcome</option>@foreach (\App\Models\FollowUp::OUTCOMES as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
                                    <div class="form-field"><label class="field-label" for="remark-{{ $followUp->id }}">Remark</label><textarea class="textarea-control" name="remark" id="remark-{{ $followUp->id }}" rows="2" placeholder="What happened on the call?"></textarea></div>
                                    <div class="form-field"><label class="field-label" for="next-{{ $followUp->id }}">Next follow-up <span class="table-muted">· optional</span></label><input class="form-control" type="datetime-local" name="next_follow_up_at" id="next-{{ $followUp->id }}"></div>
                                    <div class="form-grid"><div class="form-field"><label class="field-label" for="meeting-{{ $followUp->id }}">Meeting date/time</label><input class="form-control" type="datetime-local" name="meeting_at" id="meeting-{{ $followUp->id }}"></div><div class="form-field"><label class="field-label" for="meeting-with-{{ $followUp->id }}">Meeting with</label><input class="form-control" name="meeting_with" id="meeting-with-{{ $followUp->id }}" placeholder="Attendee"></div><div class="form-field"><label class="field-label" for="trial-{{ $followUp->id }}">Trial date/time</label><input class="form-control" type="datetime-local" name="trial_at" id="trial-{{ $followUp->id }}"></div><div class="form-field"><label class="field-label" for="trial-details-{{ $followUp->id }}">Trial details</label><input class="form-control" name="trial_details" id="trial-details-{{ $followUp->id }}" placeholder="Session details"></div></div>
                                    <div class="complete-actions"><button type="submit" class="btn btn-primary btn-sm"><x-icon name="check" size="14" /> Save outcome</button></div>
                                </form>
                            </details>
                            <form method="POST" action="{{ route('follow-ups.destroy', $followUp) }}" data-confirm="Remove this follow-up from the calendar?">@csrf @method('DELETE')<button class="row-action danger" type="submit" title="Remove follow-up"><x-icon name="trash" size="14" /></button></form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        @if ($followUps->hasPages())<div class="table-footer"><span>Showing {{ $followUps->firstItem() }}–{{ $followUps->lastItem() }} of {{ $followUps->total() }}</span><div class="pagination-links">{{ $followUps->onEachSide(1)->links('pagination.crm') }}</div></div>@endif
    @endif
@endsection
