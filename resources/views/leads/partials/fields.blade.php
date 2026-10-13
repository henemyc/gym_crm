@php($modal = $modal ?? false)
<form method="POST" action="{{ $mode === 'create' ? route('leads.store') : route('leads.update', $lead) }}" class="{{ $formClass ?? 'surface-card form-card' }}" @if($modal) data-ajax-lead-form @endif @if(!empty($placeProspectId)) data-place-prospect-id="{{ $placeProspectId }}" @endif @if($mode === 'edit') data-lead-profile-url="{{ route('leads.show', $lead) }}" @endif>
    @csrf
    @if ($mode === 'edit') @method('PUT') @endif
    <div class="lead-form-alert" data-form-errors hidden role="alert"></div>
    @if(!empty($placeProspectId))
        <input type="hidden" name="place_prospect_id" value="{{ $placeProspectId }}">
        <div class="google-verification-note"><x-icon name="shield" size="15" /><span>Enter details you independently verified from a first-party source or direct contact. Google-provided business fields are intentionally not prefilled or saved.</span></div>
        <div class="form-field google-verification-attestation">
            <label for="lead-independent-contact-verified"><input id="lead-independent-contact-verified" type="checkbox" name="independent_contact_verified" value="1" required><span>I confirm the contact name, gym name, and phone number above were independently verified, not copied solely from Google Places.</span></label>
            @error('independent_contact_verified')<span class="field-error">{{ $message }}</span>@enderror
        </div>
    @endif

    <div class="form-section lead-form-section-contact">
        <h2 class="form-section-title">Contact & gym details</h2>
        <div class="form-grid">
            <div class="form-field"><label class="field-label" for="lead-name">Contact name <span class="required">*</span></label><input class="form-control" id="lead-name" name="name" value="{{ old('name', $lead->name) }}" placeholder="e.g. Ananya Sharma" maxlength="120" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="form-field"><label class="field-label" for="lead-gym-name">Gym name <span class="required">*</span></label><input class="form-control" id="lead-gym-name" name="gym_name" value="{{ old('gym_name', $lead->gym_name) }}" placeholder="e.g. PulseFit Studio" maxlength="160" required>@error('gym_name')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="form-field"><label class="field-label" for="lead-phone">Phone number <span class="required">*</span></label><input class="form-control" id="lead-phone" name="phone" type="tel" value="{{ old('phone', $lead->phone) }}" placeholder="+91 98765 43210" maxlength="32" required>@error('phone')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="form-field"><label class="field-label" for="lead-location">Location</label><input class="form-control" id="lead-location" name="location" value="{{ old('location', $lead->location) }}" placeholder="City or area" maxlength="120">@error('location')<span class="field-error">{{ $message }}</span>@enderror</div>
        </div>
    </div>

    <div class="form-section lead-form-section-qualification">
        <h2 class="form-section-title">Qualification & ownership</h2>
        <div class="form-grid form-grid-three">
            <div class="form-field"><label class="field-label" for="lead-source">Lead source <span class="required">*</span></label><select class="select-control" id="lead-source" name="source" required>@foreach (\App\Models\Lead::SOURCES as $source)<option value="{{ $source }}" @selected(old('source', $lead->source) === $source)>{{ $source }}</option>@endforeach</select>@error('source')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="form-field"><label class="field-label" for="lead-priority">Priority <span class="required">*</span></label><select class="select-control" id="lead-priority" name="priority" required>@foreach (\App\Models\Lead::PRIORITIES as $key => $label)<option value="{{ $key }}" @selected(old('priority', $lead->priority) === $key)>{{ $label }}</option>@endforeach</select>@error('priority')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="form-field"><label class="field-label" for="lead-status">Status <span class="required">*</span></label><select class="select-control" id="lead-status" name="status" required>@foreach (\App\Models\Lead::STATUSES as $key => $label)<option value="{{ $key }}" @selected(old('status', $lead->status) === $key)>{{ $label }}</option>@endforeach</select>@error('status')<span class="field-error">{{ $message }}</span>@enderror</div>
            @if (auth()->user()->canManageTeam())
                <div class="form-field"><label class="field-label" for="lead-assigned-to">Assigned to</label><select class="select-control" id="lead-assigned-to" name="assigned_to"><option value="">Unassigned</option>@foreach ($team as $member)<option value="{{ $member->id }}" @selected((string) old('assigned_to', $lead->assigned_to) === (string) $member->id)>{{ $member->name }} · {{ \App\Models\User::ROLES[$member->role] ?? 'Team' }}</option>@endforeach</select>@error('assigned_to')<span class="field-error">{{ $message }}</span>@enderror</div>
            @endif
        </div>
    </div>

    <div class="form-section lead-form-section-notes">
        <h2 class="form-section-title">Context for your next conversation</h2>
        <div class="form-field"><label class="field-label" for="lead-notes">Notes <span class="field-optional">· optional</span></label><textarea class="textarea-control" id="lead-notes" name="notes" maxlength="10000" placeholder="Timing, membership size, current tools, or anything that helps your team follow through.">{{ old('notes', $lead->notes) }}</textarea><span class="field-hint">Keep the context specific and easy for a teammate to act on.</span>@error('notes')<span class="field-error">{{ $message }}</span>@enderror</div>
    </div>

    <div class="form-actions">
        @if ($modal)
            <button class="btn btn-secondary" type="button" data-lead-dialog-close>Cancel</button>
        @else
            <a class="btn btn-secondary" href="{{ $mode === 'edit' ? route('leads.show', $lead) : route('leads.index') }}">Cancel</a>
        @endif
        <button class="btn btn-primary" type="submit"><x-icon name="check" size="16" /> {{ $mode === 'create' ? 'Create lead' : 'Save changes' }}</button>
    </div>
</form>
