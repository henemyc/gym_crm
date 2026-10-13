@forelse($team as $member)
    @include('settings.partials.member-row', ['member' => $member, 'viewerTimezone' => $viewerTimezone])
@empty
    <tr class="staff-empty-row"><td colspan="6"><div class="empty-state empty-state-compact"><span class="empty-icon"><x-icon name="users" size="22" /></span><strong>No team members yet</strong><p>Add a teammate to start building your workspace team.</p></div></td></tr>
@endforelse
