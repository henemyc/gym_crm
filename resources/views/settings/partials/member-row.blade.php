@php
    $viewer = auth()->user();
    $palette = ['orange', 'blue', 'green', 'purple', 'rose'];
    $canEditMember = $viewer->isAdmin() || ($member->role === 'sales' && $member->id !== $viewer->id);
    $canDeleteMember = $member->id !== $viewer->id && ($viewer->isAdmin() || $member->role === 'sales');
@endphp
<tr data-team-presence-row="{{ $member->id }}">
    <td>
        <div class="team-member">
            <span class="avatar avatar-soft avatar-{{ $palette[$member->id % count($palette)] }}">{{ $member->initials() }}</span>
            <span>
                <strong>{{ $member->name }} @if($member->id === $viewer->id)<span class="table-muted">(you)</span>@endif</strong>
                <small>{{ $member->email }}{{ $member->phone ? ' · '.$member->phone : '' }}</small>
            </span>
        </div>
    </td>
    <td><span class="role-badge {{ $member->role }}">{{ \App\Models\User::ROLES[$member->role] ?? 'Member' }}</span></td>
    <td><span class="staff-lead-count">{{ number_format($member->assigned_leads_count) }}</span></td>
    <td>
        @if($member->is_active && $member->isOnlineNow())
            <span class="presence-state presence-online" data-team-presence-state><i class="presence-dot"></i><span data-team-presence-label>Online now</span></span>
            <small class="staff-presence-detail" data-team-presence-detail>Active {{ $member->last_activity_at->diffForHumans() }}</small>
        @elseif($member->last_activity_at)
            <span class="presence-state presence-offline" data-team-presence-state><i class="presence-dot"></i><span data-team-presence-label>{{ $member->is_active ? 'Offline' : 'Inactive' }}</span></span>
            <small class="staff-presence-detail" data-team-presence-detail>Last seen {{ $member->last_activity_at->diffForHumans() }}</small>
        @else
            <span class="presence-state presence-unknown" data-team-presence-state><i class="presence-dot"></i><span data-team-presence-label>{{ $member->is_active ? 'Not seen yet' : 'Inactive' }}</span></span>
            <small class="staff-presence-detail" data-team-presence-detail>No activity recorded</small>
        @endif
    </td>
    <td data-team-login-cell>
        @if($member->last_login_at)
            <span class="staff-login-date" data-team-login-date>{{ $member->last_login_at->timezone($viewerTimezone)->format('d M Y') }}</span>
            <small class="staff-presence-detail" data-team-login-time>{{ $member->last_login_at->timezone($viewerTimezone)->format('g:i A') }}</small>
        @else
            <span class="staff-login-date staff-login-never" data-team-login-date>Never signed in</span>
            <small class="staff-presence-detail" data-team-login-time>First sign-in pending</small>
        @endif
    </td>
    <td>
        @if($canEditMember || $canDeleteMember)
            <div class="staff-row-actions">
                @if($canEditMember)
                    <button class="btn btn-secondary btn-sm" type="button" data-team-edit
                        data-update-url="{{ route('settings.users.update', $member) }}"
                        data-member-name="{{ $member->name }}"
                        data-member-email="{{ $member->email }}"
                        data-member-phone="{{ $member->phone }}"
                        data-member-role="{{ $member->role }}"
                        data-member-active="{{ $member->is_active ? '1' : '0' }}"
                        data-member-self="{{ $member->id === $viewer->id ? 'true' : 'false' }}">
                        <x-icon name="edit" size="14" /> Edit
                    </button>
                @endif
                @if($canDeleteMember)
                    <button class="btn btn-secondary btn-sm staff-remove-btn" type="button" data-team-delete
                        data-delete-url="{{ route('settings.users.destroy', $member) }}"
                        data-member-name="{{ $member->name }}">
                        <x-icon name="trash" size="14" /> Remove
                    </button>
                @endif
            </div>
        @else
            <span class="table-muted">Managed by admin</span>
        @endif
    </td>
</tr>
