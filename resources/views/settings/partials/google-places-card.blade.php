<section class="surface-card form-card google-places-settings-card" data-google-settings-card>
    <div class="card-heading google-settings-heading">
        <div>
            <h2>Google Places integration</h2>
            <p>Configure server-side gym discovery and review live API usage safeguards.</p>
        </div>
        <span class="status-pill {{ $placesIntegration['configured'] ? 'status-converted' : 'status-orange' }}" data-google-key-state>
            {{ $placesIntegration['configured'] ? 'Key saved' : 'Not connected' }}
        </span>
    </div>

    <div class="google-key-status" data-google-key-status role="status" aria-live="polite">
        @if($placesIntegration['configured'])
            <strong>Configured key:</strong> {{ $placesIntegration['key_hint'] }}.
            @if($placesIntegration['verified_at'])
                Last verified {{ $placesIntegration['verified_at']->format('d M Y, g:i A') }}.
            @else
                Connection has not been tested after the latest key change.
            @endif
        @else
            Add an API key restricted to the server IP and to Places API (New) plus Geocoding API.
        @endif
    </div>

    <form class="google-key-form" method="POST" action="{{ route('settings.google-places.update') }}" data-google-settings-form data-no-loading>
        @csrf
        @method('PUT')
        <div class="form-field">
            <label class="field-label" for="google-places-api-key">Google Maps Platform API key</label>
            <div class="google-key-input-row">
                <input class="form-control" id="google-places-api-key" type="password" name="api_key" minlength="20" maxlength="255" autocomplete="new-password" spellcheck="false" autocapitalize="off" placeholder="Paste a new server-side key" required>
                <button class="btn btn-primary" type="submit" data-google-key-save>Save key</button>
            </div>
            <span class="field-hint">Encrypted in the database; never sent to the browser. Saving a replacement clears the previous verification.</span>
            @error('api_key')<span class="field-error">{{ $message }}</span>@enderror
        </div>
        <div class="google-key-actions">
            <button class="btn btn-secondary btn-sm" type="button" data-google-key-test data-test-url="{{ route('settings.google-places.test') }}" @disabled(! $placesIntegration['configured'])>
                <x-icon name="activity" size="14" /> Test connection
            </button>
            <small class="google-test-note">A test uses one Geocoding request and one Places Text Search Pro request.</small>
            <button class="btn btn-secondary btn-sm google-disconnect-btn" type="button" data-google-key-clear data-delete-url="{{ route('settings.google-places.destroy') }}" @unless($placesIntegration['configured']) hidden @endunless>
                Disconnect key
            </button>
            <a class="google-cloud-help-link" href="https://console.cloud.google.com/google/maps-apis/credentials" target="_blank" rel="noopener noreferrer">Manage keys in Google Cloud <x-icon name="external" size="13" /></a>
        </div>
    </form>

    <div class="google-usage-panel" aria-labelledby="google-usage-title">
        <div class="google-usage-heading">
            <div><h3 id="google-usage-title">GymXBook monthly request guard</h3><p>Billing month {{ $placesUsage['month'] }} · resets on Google billing time ({{ $placesUsage['timezone'] }}).</p></div>
            <span class="status-pill status-orange">Hard stop enabled</span>
        </div>
        <div class="google-usage-grid">
            @foreach($placesUsage['categories'] as $sku => $usage)
                <div class="google-usage-item" data-google-usage-sku="{{ $sku }}">
                    <div class="google-usage-item-heading"><span>{{ $usage['label'] }}</span><strong><span data-google-usage-value>{{ number_format($usage['used']) }}</span> <small>/ {{ number_format($usage['cap']) }}</small></strong></div>
                    <div class="google-usage-track" role="progressbar" aria-label="{{ $usage['label'] }}" aria-valuemin="0" aria-valuemax="{{ $usage['cap'] }}" aria-valuenow="{{ $usage['used'] }}" data-google-usage-progress><span style="width: {{ $usage['percent'] }}%"></span></div>
                    <small><span>{{ $usage['tier'] }}</span> · <span data-google-usage-remaining>{{ number_format($usage['remaining']) }}</span> requests left in the GymXBook cap</small>
                </div>
            @endforeach
        </div>
        <p class="google-usage-warning"><x-icon name="alert" size="15" /> These are per-app safeguards, not a Google Cloud invoice meter. They assume an India billing profile and leave headroom below current India free caps; usage by other projects or APIs on the same billing account can consume that allowance. Google Cloud budget alerts do not stop charges—set API quotas lower than the account’s remaining free allowance.</p>
    </div>
</section>
