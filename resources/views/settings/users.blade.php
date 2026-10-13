@extends('layouts.app')
@section('title', 'Staff & activity')
@section('page-name', 'Team')

@section('content')
    <div class="breadcrumb"><a href="{{ route('settings.index') }}">Settings</a><x-icon name="chevron-right" size="13" /><span>Staff & activity</span></div>
    <div class="page-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-dot"></span> YOUR WORKSPACE TEAM</div>
            <h1>Staff & activity</h1>
            <p class="page-description">Manage team access and see who’s active across your sales workspace.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('settings.index') }}"><x-icon name="arrow-left" size="15" /> Settings</a>
    </div>

    <div class="staff-overview-grid" aria-label="Team summary" data-team-presence-url="{{ route('settings.users.presence') }}">
        <article class="staff-overview-card">
            <span class="staff-overview-icon"><x-icon name="users" size="17" /></span>
            <div><small>Team members</small><strong data-team-stat="total">{{ $teamCount }}</strong><span>Across all workspace roles</span></div>
        </article>
        <article class="staff-overview-card">
            <span class="staff-overview-icon staff-overview-icon-green"><x-icon name="check-circle" size="17" /></span>
            <div><small>Active accounts</small><strong data-team-stat="active">{{ $activeCount }}</strong><span data-team-stat="sales-label">{{ $salesCount }} active sales {{ \Illuminate\Support\Str::plural('rep', $salesCount) }}</span></div>
        </article>
        <article class="staff-overview-card">
            <span class="staff-overview-icon staff-overview-icon-online"><span class="presence-dot"></span></span>
            <div><small>Online now</small><strong data-team-online-count>{{ $onlineCount }}</strong><span>Active within the last 5 minutes</span></div>
        </article>
    </div>

    <section class="surface-card table-card staff-team-card" data-team-panel>
        <div class="card-heading staff-table-heading">
            <div><h2>Workspace members</h2><p>Last login, live presence and assigned lead totals.</p></div>
            <div class="staff-table-controls">
                <span class="status-pill status-orange" data-team-active-label>{{ $activeCount }} active</span>
                <button class="btn btn-primary" type="button" data-team-modal-open data-team-create>
                    <x-icon name="plus" size="15" /> Add teammate
                </button>
            </div>
        </div>
        <div class="team-table-wrap">
            <table class="team-table staff-table">
                <thead><tr><th>Member</th><th>Role</th><th>Assigned leads</th><th>Presence</th><th>Last login</th><th>Manage</th></tr></thead>
                <tbody data-team-rows>
                    @include('settings.partials.member-rows', ['team' => $team, 'viewerTimezone' => $viewerTimezone])
                </tbody>
            </table>
        </div>
        <div class="staff-access-note"><x-icon name="shield" size="16" /><span><strong>Lead access:</strong> Admins and Managers can view all leads. Sales reps can view only leads assigned directly to them.</span></div>
    </section>

    <div class="team-modal-backdrop" data-team-modal hidden aria-hidden="true">
        <section class="team-modal" role="dialog" aria-modal="true" aria-labelledby="team-modal-title" aria-describedby="team-modal-description" tabindex="-1">
            <div class="team-modal-header">
                <span class="team-modal-mark"><x-icon name="users" size="18" /></span>
                <div>
                    <span class="eyebrow">WORKSPACE ACCESS</span>
                    <h2 id="team-modal-title" data-team-modal-title>Add a teammate</h2>
                    <p id="team-modal-description" data-team-modal-description>Create a secure account for your workspace.</p>
                </div>
                <button class="icon-button" type="button" data-team-modal-close aria-label="Close teammate form"><x-icon name="x" size="17" /></button>
            </div>
            <form class="team-modal-form" method="POST" action="{{ route('settings.users.store') }}" data-team-form data-no-loading>
                @csrf
                <input type="hidden" name="_method" value="PATCH" data-team-method disabled>
                <div class="form-error-summary team-form-error-summary" data-form-errors role="alert" hidden></div>
                <div class="team-form-grid">
                    <div class="form-field"><label class="field-label" for="team-name">Full name</label><input class="form-control" id="team-name" name="name" autocomplete="name" maxlength="120" required></div>
                    <div class="form-field"><label class="field-label" for="team-email">Work email</label><input class="form-control" id="team-email" name="email" type="email" autocomplete="email" maxlength="255" required></div>
                    <div class="form-field"><label class="field-label" for="team-phone">Phone <span class="table-muted">· optional</span></label><input class="form-control" id="team-phone" name="phone" type="tel" autocomplete="tel" maxlength="30"></div>
                    <div class="form-field"><label class="field-label" for="team-role">Role</label>
                        <select class="select-control" id="team-role" name="role" required>
                            @foreach(\App\Models\User::ROLES as $key => $label)
                                @if(auth()->user()->isAdmin() || $key === 'sales')<option value="{{ $key }}" @selected($key === 'sales')>{{ $label }}</option>@endif
                            @endforeach
                        </select>
                        @unless(auth()->user()->isAdmin())<span class="field-hint">Managers can add and manage Sales reps.</span>@endunless
                    </div>
                    <div class="form-field team-modal-edit-only" data-team-active-field hidden><label class="field-label" for="team-active">Account access</label>
                        <select class="select-control" id="team-active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <span class="field-hint">Inactive members cannot sign in.</span>
                    </div>
                    <div class="team-password-fields" data-team-password-fields>
                        <div class="form-field"><label class="field-label" for="team-password">Temporary password</label><input class="form-control" id="team-password" name="password" type="password" autocomplete="new-password" minlength="10" required><span class="field-hint">At least 10 characters; ask them to update it after sign-in.</span></div>
                        <div class="form-field"><label class="field-label" for="team-password-confirmation">Confirm password</label><input class="form-control" id="team-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="10" required></div>
                    </div>
                </div>
                <div class="team-modal-actions">
                    <button class="btn btn-secondary" type="button" data-team-modal-close>Cancel</button>
                    <button class="btn btn-primary" type="submit" data-team-submit><x-icon name="plus" size="15" /><span data-team-submit-label>Add teammate</span></button>
                </div>
            </form>
        </section>
    </div>
@endsection
