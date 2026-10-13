<header class="topbar">
    <div class="topbar-left">
        <button type="button" class="icon-button mobile-menu" data-sidebar-toggle aria-label="Open navigation"><x-icon name="menu" size="21" /></button>
        <div class="topbar-crumb"><span class="brand-word">GymXBook</span><x-icon name="chevron-right" size="14" /><strong>@yield('page-name', 'Overview')</strong></div>
    </div>
    <div class="topbar-actions">
        <form class="global-search" action="{{ route('leads.index') }}" method="GET" role="search">
            <x-icon name="search" size="17" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Search leads or gyms…" aria-label="Search leads">
            <kbd>Ctrl K</kbd>
        </form>
        <div class="dropdown-wrap">
            <button type="button" class="icon-button notification-button" data-dropdown-toggle="notifications-menu" aria-label="Notifications">
                <x-icon name="bell" size="19" />
                @if(($sidebarDueCount ?? 0) > 0)<span class="notification-ping"></span>@endif
            </button>
            <div class="dropdown-panel notification-panel" id="notifications-menu">
                <div class="dropdown-heading"><strong>Today at a glance</strong><span class="status-pill status-orange">{{ $sidebarDueCount ?? 0 }} due</span></div>
                <p class="dropdown-note">{{ ($sidebarDueCount ?? 0) > 0 ? 'A few conversations are ready for your attention.' : 'You’re all caught up. Nice work.' }}</p>
                <a class="dropdown-action" href="{{ route('follow-ups.index', ['view' => 'today']) }}">Open follow-ups <x-icon name="arrow-right" size="15" /></a>
            </div>
        </div>
        <span class="topbar-divider"></span>
        <div class="dropdown-wrap">
            <button type="button" class="profile-trigger" data-dropdown-toggle="profile-menu">
                <span class="avatar avatar-orange avatar-sm">{{ auth()->user()->initials() }}</span>
                <span class="profile-trigger-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ \App\Models\User::ROLES[auth()->user()->role] ?? 'Team member' }}</small></span>
                <x-icon name="chevron-down" size="15" />
            </button>
            <div class="dropdown-panel profile-menu" id="profile-menu">
                <div class="dropdown-profile"><span class="avatar avatar-orange">{{ auth()->user()->initials() }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></div></div>
                <a href="{{ route('settings.index') }}"><x-icon name="settings" size="16" /> Account settings</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><x-icon name="log-out" size="16" /> Sign out</button></form>
            </div>
        </div>
    </div>
</header>
