<aside class="sidebar sidebar-light" id="app-sidebar" aria-label="Main navigation">
    <div class="sidebar-top">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="GymXBook home">
            <span class="brand-mark"><x-icon name="spark" size="19" /></span>
            <span class="brand-copy"><strong>GymXBook</strong><small>GROWTH DESK</small></span>
        </a>
        <button class="sidebar-close icon-button" type="button" data-sidebar-close aria-label="Close navigation"><x-icon name="x" size="19" /></button>
    </div>

    <nav class="sidebar-navigation" aria-label="Workspace navigation">
        <div class="nav-group">
            <div class="nav-label">OVERVIEW</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><x-icon name="grid" size="19" /><span>Dashboard</span></a>
        </div>
        <div class="nav-group">
            <div class="nav-label">SALES WORKSPACE</div>
            <a href="{{ route('leads.index') }}" class="nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}"><x-icon name="users" size="19" /><span>Leads</span><span class="nav-counter">{{ $sidebarLeadCount ?? '' }}</span></a>
            <a href="{{ route('prospecting.index') }}" class="nav-link {{ request()->routeIs('prospecting.*') ? 'active' : '' }}"><x-icon name="map-pin" size="19" /><span>Prospecting</span></a>
            <a href="{{ route('follow-ups.index') }}" class="nav-link {{ request()->routeIs('follow-ups.*') ? 'active' : '' }}"><x-icon name="clock" size="19" /><span>Follow-ups</span>@if(($sidebarDueCount ?? 0) > 0)<span class="nav-badge">{{ $sidebarDueCount }}</span>@endif</a>
            <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}"><x-icon name="calendar" size="19" /><span>Calendar</span></a>
        </div>
        <div class="nav-group">
            <div class="nav-label">INSIGHTS & TOOLS</div>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><x-icon name="chart" size="19" /><span>Reports</span></a>
            <a href="{{ route('imports.index') }}" class="nav-link {{ request()->routeIs('imports.*') ? 'active' : '' }}"><x-icon name="upload" size="19" /><span>Import leads</span></a>
        </div>
        @if(auth()->user()->canManageTeam())
            <div class="nav-group">
                <div class="nav-label">MANAGEMENT</div>
                <a href="{{ route('settings.users') }}" class="nav-link {{ request()->routeIs('settings.users*') ? 'active' : '' }}"><x-icon name="users" size="19" /><span>Team</span></a>
            </div>
        @endif
    </nav>

    <div class="sidebar-bottom">
        <a href="{{ route('settings.index') }}" class="nav-link nav-settings {{ request()->routeIs('settings.index') ? 'active' : '' }}"><x-icon name="settings" size="19" /><span>Settings</span></a>
        <div class="sidebar-help">
            <div class="help-orbit"><x-icon name="sparkles" size="18" /></div>
            <strong>Make the next move clear</strong>
            <p>Review your numbers and focus on the opportunities that need attention.</p>
            <a href="{{ route('reports.index') }}">Explore reports <x-icon name="arrow-right" size="14" /></a>
        </div>
        <div class="sidebar-user">
            <span class="avatar avatar-orange">{{ auth()->user()->initials() }}</span>
            <span class="sidebar-user-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ \App\Models\User::ROLES[auth()->user()->role] ?? 'Team member' }}</small></span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="icon-button logout-button" type="submit" aria-label="Sign out" title="Sign out"><x-icon name="log-out" size="17" /></button></form>
        </div>
    </div>
</aside>
<div class="sidebar-scrim" data-sidebar-close></div>
