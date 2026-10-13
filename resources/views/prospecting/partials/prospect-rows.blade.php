@forelse($prospects as $prospect)
    @include('prospecting.partials.prospect-row', ['prospect' => $prospect, 'canManageTeam' => $canManageTeam, 'team' => $team])
@empty
    <tr class="place-prospect-empty"><td colspan="5"><div class="empty-state empty-state-compact"><span class="empty-icon"><x-icon name="map-pin" size="22" /></span><strong>No open Google Maps prospects</strong><p>{{ $canManageTeam ? 'Search a location, select gyms, and assign prospects to your team.' : 'Your assigned prospects will appear here when they are ready for follow-up.' }}</p></div></td></tr>
@endforelse
