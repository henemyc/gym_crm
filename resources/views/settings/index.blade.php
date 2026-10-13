@extends('layouts.app')
@section('title', 'Settings')
@section('page-name', 'Settings')

@section('content')
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> YOUR SPACE, YOUR RHYTHM</div><h1>Settings</h1><p class="page-description">Manage your account, preferences, and the people in your workspace.</p></div></div>
    <div class="settings-layout">
        <div class="settings-main-stack">
            <section class="surface-card form-card"><div class="card-heading" style="padding:0 0 17px"><div><h2>Profile details</h2><p>How your teammates see you around <span class="brand-word">GymXBook</span>.</p></div><span class="status-pill status-orange">{{ \App\Models\User::ROLES[auth()->user()->role] ?? 'Team member' }}</span></div>
                @if ($errors->any())<div class="form-error-summary">{{ $errors->first() }}</div>@endif
                <form method="POST" action="{{ route('settings.profile') }}">@csrf @method('PUT')
                    <div class="form-grid"><div class="form-field"><label class="field-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required></div><div class="form-field"><label class="field-label" for="email">Work email</label><input class="form-control" type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required></div><div class="form-field"><label class="field-label" for="phone">Phone number</label><input class="form-control" type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+91 98765 43210"></div><div class="form-field"><label class="field-label" for="timezone">Timezone</label><select class="select-control" id="timezone" name="timezone">@foreach(['Asia/Kolkata'=>'India Standard Time · Kolkata','Asia/Dubai'=>'Gulf Standard Time · Dubai','Asia/Singapore'=>'Singapore Standard Time','Europe/London'=>'Greenwich Mean Time · London','America/New_York'=>'Eastern Time · New York','UTC'=>'Coordinated Universal Time'] as $value=>$label)<option value="{{ $value }}" @selected(old('timezone', auth()->user()->timezone) === $value)>{{ $label }}</option>@endforeach</select></div></div>
                    <div class="form-actions"><button class="btn btn-primary" type="submit"><x-icon name="check" size="15" /> Save profile</button></div>
                </form>
            </section>
            <section class="surface-card form-card"><div class="card-heading" style="padding:0 0 17px"><div><h2>Security</h2><p>Keep your <span class="brand-word">GymXBook</span> workspace in good hands.</p></div><span class="metric-icon metric-icon-green"><x-icon name="shield" size="17" /></span></div>
                <form method="POST" action="{{ route('settings.password') }}">@csrf @method('PUT')
                    <div class="form-grid"><div class="form-field"><label class="field-label" for="current_password">Current password</label><input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password" required></div><div class="form-field"><label class="field-label" for="password">New password</label><input class="form-control" type="password" id="password" name="password" autocomplete="new-password" required><span class="field-hint">At least 10 characters with letters and numbers.</span></div><div class="form-field"><label class="field-label" for="password_confirmation">Confirm new password</label><input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required></div></div>
                    <div class="form-actions"><button class="btn btn-secondary" type="submit">Update password</button></div>
                </form>
            </section>
            <section class="surface-card form-card"><div class="card-heading" style="padding:0 0 13px"><div><h2>Notifications</h2><p>Choose how <span class="brand-word">GymXBook</span> keeps you in the loop.</p></div></div>
                <form method="POST" action="{{ route('settings.notifications') }}">@csrf @method('PUT')<div class="setting-line"><div><strong>Workspace reminders</strong><small>Keep follow-up and assignment reminders enabled in your workspace.</small></div><label class="toggle"><input type="checkbox" name="notifications_enabled" value="1" @checked(auth()->user()->notifications_enabled)><span class="toggle-track"></span></label></div><div class="form-actions"><button class="btn btn-secondary" type="submit">Save preferences</button></div></form>
            </section>
            @if(auth()->user()->isAdmin())
                @include('settings.partials.google-places-card', ['placesIntegration' => $placesIntegration, 'placesUsage' => $placesUsage])
            @endif
        </div>
        <aside class="settings-side-stack"><section class="surface-card settings-nav"><a class="active" href="{{ route('settings.index') }}"><x-icon name="user" size="16" /> Profile & preferences</a>@if(auth()->user()->canManageTeam())<a href="{{ route('settings.users') }}"><x-icon name="users" size="16" /> Staff & activity</a>@endif</section><section class="surface-card settings-info-card"><h3><span class="brand-word">GymXBook</span> Fitness</h3><p>Workspace settings are managed by the people responsible for your sales team.</p><div class="setting-line"><div><strong>Your role</strong><small>Controls what you can view and manage.</small></div><span class="role-badge {{ auth()->user()->role }}">{{ \App\Models\User::ROLES[auth()->user()->role] ?? 'Member' }}</span></div>@if(auth()->user()->canManageTeam())<a class="btn btn-secondary btn-sm" href="{{ route('settings.users') }}">Open staff panel <x-icon name="arrow-right" size="14" /></a>@endif</section><section class="surface-card settings-info-card"><h3>Need a hand?</h3><p>Your team admin can help with access, assignment, and workspace questions.</p></section></aside>
    </div>
@endsection
