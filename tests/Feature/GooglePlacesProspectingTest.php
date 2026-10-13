<?php

namespace Tests\Feature;

use App\Exceptions\GooglePlacesException;
use App\Models\GooglePlaceProspect;
use App\Models\GooglePlaceProspectActivity;
use App\Models\Lead;
use App\Models\User;
use App\Models\WorkspaceSetting;
use App\Services\GooglePlacesClient;
use App\Services\GooglePlacesUsageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GooglePlacesProspectingTest extends TestCase
{
    use RefreshDatabase;

    private const API_KEY = 'TEST_GOOGLE_PLACES_KEY_1234567890';
    private const SEARCH_MASK = 'places.id,places.displayName,places.formattedAddress,places.googleMapsUri,places.location,nextPageToken';
    private const DETAILS_MASK = 'id,displayName,formattedAddress,nationalPhoneNumber,userRatingCount,googleMapsUri';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_radius_search_uses_pro_fields_and_search_more_reuses_the_geocoded_center(): void
    {
        $manager = User::factory()->manager()->create();
        WorkspaceSetting::putSecret('google_places_api_key', self::API_KEY);
        $firstPlace = $this->googlePlace('ChIJGymProspect0001', 'Strength House', 26.8468, 80.9463);
        // Defensively prove Enterprise-only fields are stripped even if a mocked/upstream payload contains them.
        $firstPlace['nationalPhoneNumber'] = '+91 99999 11111';
        $firstPlace['userRatingCount'] = 999;
        $secondPlace = $this->googlePlace('ChIJGymProspect0002', 'Core Club', 26.8470, 80.9464);

        Http::fake(function (ClientRequest $request) use ($firstPlace, $secondPlace) {
            if (str_contains($request->url(), 'maps.googleapis.com/maps/api/geocode/json')) {
                return Http::response($this->geocodeResponse(), 200);
            }

            if (str_contains($request->url(), 'places:searchText')) {
                $pageToken = $request->data()['pageToken'] ?? null;
                return $pageToken
                    ? Http::response(['places' => [$secondPlace], 'nextPageToken' => 'page-three-token'], 200)
                    : Http::response(['places' => [$firstPlace], 'nextPageToken' => 'page-two-token'], 200);
            }

            return Http::response(['message' => 'Unexpected outbound request'], 500);
        });

        $payload = ['location' => 'Gomti Nagar, Lucknow', 'radius_km' => 5];
        $firstPage = $this->actingAs($manager)->postJson(route('prospecting.search'), $payload)
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private, max-age=0')
            ->assertJsonPath('page', 1)
            ->assertJsonPath('max_pages', 2)
            ->assertJsonPath('results.0.name', 'Strength House')
            ->assertJsonPath('results.0.address', 'Gomti Nagar, Lucknow, India');

        $this->assertArrayNotHasKey('phone', $firstPage->json('results.0'));
        $this->assertArrayNotHasKey('review_count', $firstPage->json('results.0'));
        $this->assertNotEmpty($firstPage->json('next_page_state'));
        $this->postJson(route('prospecting.search'), [...$payload, 'page_state' => $firstPage->json('next_page_state')])
            ->assertOk()
            ->assertJsonPath('page', 2)
            ->assertJsonPath('max_pages', 2)
            ->assertJsonPath('next_page_state', null)
            ->assertJsonPath('results.0.name', 'Core Club');

        Http::assertSent(fn (ClientRequest $request): bool => $request->method() === 'POST'
            && str_contains($request->url(), 'places:searchText')
            && $request->hasHeader('X-Goog-FieldMask', self::SEARCH_MASK));
        $this->assertDatabaseHas('google_places_api_usage', ['sku' => 'geocoding', 'reserved_requests' => 1]);
        $this->assertDatabaseHas('google_places_api_usage', ['sku' => 'text_search_pro', 'reserved_requests' => 2]);
        $this->assertDatabaseMissing('google_places_api_usage', ['sku' => 'place_details_enterprise']);
    }

    public function test_saved_prospect_details_are_an_explicit_enterprise_request_and_are_not_persisted(): void
    {
        $sales = User::factory()->create();
        $prospect = GooglePlaceProspect::query()->create([
            'place_id' => 'ChIJGymProspectDetails01',
            'assigned_to' => $sales->id,
            'created_by' => $sales->id,
            'status' => 'new',
        ]);
        WorkspaceSetting::putSecret('google_places_api_key', self::API_KEY);
        Http::fake(fn (ClientRequest $request) => Http::response([
            'id' => $prospect->place_id,
            'displayName' => ['text' => 'Strength House'],
            'formattedAddress' => 'Gomti Nagar, Lucknow, India',
            'nationalPhoneNumber' => '+91 98765 43210',
            'userRatingCount' => 87,
            'googleMapsUri' => 'https://maps.google.com/?cid=12345',
        ], 200));

        $this->actingAs($sales)->getJson(route('prospecting.details', $prospect))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private, max-age=0')
            ->assertJsonPath('place.phone', '+91 98765 43210')
            ->assertJsonPath('place.review_count', 87)
            ->assertJsonPath('notice', 'Live Google Maps information. GymXBook does not save these Google-provided fields.');

        Http::assertSent(fn (ClientRequest $request): bool => $request->method() === 'GET'
            && str_ends_with($request->url(), '/'.rawurlencode($prospect->place_id))
            && $request->hasHeader('X-Goog-FieldMask', self::DETAILS_MASK));
        $this->assertDatabaseHas('google_places_api_usage', ['sku' => 'place_details_enterprise', 'reserved_requests' => 1]);
        $this->assertFalse(Schema::hasColumn('google_place_prospects', 'phone'));
        $this->assertFalse(Schema::hasColumn('google_place_prospects', 'name'));
        $this->assertFalse(Schema::hasColumn('google_place_prospects', 'formatted_address'));
    }

    public function test_only_authorized_roles_can_search_and_only_assignees_or_managers_can_fetch_details(): void
    {
        $sales = User::factory()->create();
        $otherSales = User::factory()->create();
        $prospect = GooglePlaceProspect::query()->create([
            'place_id' => 'ChIJGymProspectAccess01',
            'assigned_to' => $sales->id,
            'created_by' => $sales->id,
            'status' => 'new',
        ]);
        Http::fake(fn () => Http::response([], 200));

        $this->actingAs($otherSales)->postJson(route('prospecting.search'), [
            'location' => 'Lucknow', 'radius_km' => 5,
        ])->assertForbidden();
        $this->getJson(route('prospecting.details', $prospect))->assertForbidden();

        Http::assertNothingSent();
        $this->assertDatabaseMissing('google_places_api_usage', ['sku' => 'place_details_enterprise']);
    }

    public function test_assignment_persists_only_place_id_and_internal_workflow_and_skips_duplicates(): void
    {
        $manager = User::factory()->manager()->create();
        $sales = User::factory()->create();
        WorkspaceSetting::putSecret('google_places_api_key', self::API_KEY);
        $place = $this->googlePlace('ChIJGymProspectAssign01', 'Pulse Fitness', 26.8468, 80.9463);
        $this->fakeSearchApis([$place]);

        $search = $this->actingAs($manager)->postJson(route('prospecting.search'), [
            'location' => 'Lucknow', 'radius_km' => 5,
        ])->assertOk();
        $token = $search->json('results.0.selection_token');
        $this->assertNotEmpty($token);

        $this->postJson(route('prospecting.assign'), [
            'candidates' => [$token],
            'assigned_to' => $sales->id,
        ])->assertCreated()
            ->assertJsonPath('added_count', 1)
            ->assertJsonPath('summary.total', 1)
            ->assertJsonPath('leads_url', route('leads.index', ['places_assigned' => 1]).'#google-maps-prospects');

        $this->assertDatabaseHas('google_place_prospects', [
            'place_id' => 'ChIJGymProspectAssign01',
            'assigned_to' => $sales->id,
            'created_by' => $manager->id,
            'status' => 'new',
            'lead_id' => null,
        ]);
        $prospect = GooglePlaceProspect::query()->where('place_id', 'ChIJGymProspectAssign01')->firstOrFail();
        $this->assertDatabaseHas('google_place_prospect_activities', [
            'prospect_id' => $prospect->id,
            'actor_id' => $manager->id,
            'event' => 'created',
            'to_assigned_to' => $sales->id,
        ]);
        $this->assertFalse(Schema::hasColumn('google_place_prospects', 'phone'));

        $this->postJson(route('prospecting.assign'), [
            'candidates' => [$token],
            'assigned_to' => $manager->id,
        ])->assertOk()->assertJsonPath('added_count', 0)->assertJsonPath('duplicate_count', 1);
        $this->assertSame($sales->id, $prospect->fresh()->assigned_to);
        $this->assertSame(1, GooglePlaceProspectActivity::query()->where('prospect_id', $prospect->id)->where('event', 'created')->count());
    }

    public function test_assigned_place_prospects_appear_in_leads_and_are_scoped_to_the_assignee(): void
    {
        $manager = User::factory()->manager()->create();
        $sales = User::factory()->create();
        $otherSales = User::factory()->create();
        $prospect = GooglePlaceProspect::query()->create([
            'place_id' => 'ChIJGymLeadsView0001',
            'assigned_to' => $sales->id,
            'created_by' => $manager->id,
            'status' => 'new',
        ]);

        $this->actingAs($manager)->get(route('leads.index'))
            ->assertOk()
            ->assertSee('Google Maps prospects')
            ->assertSee('Google Maps')
            ->assertSee('Assign prospect to teammate');

        $this->actingAs($sales)->get(route('leads.index'))
            ->assertOk()
            ->assertSee('Place ID ChIJGym…0001')
            ->assertSee('Google Maps')
            ->assertDontSee('Assign prospect to teammate');

        $this->actingAs($otherSales)->get(route('leads.index'))
            ->assertOk()
            ->assertDontSee('Place ID ChIJGym…0001')
            ->assertSee('No open Google Maps prospects');

        $this->assertNull($prospect->fresh()->lead_id);
    }

    public function test_conversion_uses_user_verified_details_and_links_the_standard_lead(): void
    {
        $sales = User::factory()->create();
        $prospect = GooglePlaceProspect::query()->create([
            'place_id' => 'ChIJGymProspectConvert01',
            'assigned_to' => $sales->id,
            'created_by' => $sales->id,
            'status' => 'qualified',
        ]);
        $formHtml = $this->actingAs($sales)->getJson(route('prospecting.convert-form', $prospect))
            ->assertOk()
            ->json('html');
        $this->assertStringContainsString('independent_contact_verified', $formHtml);
        $this->assertStringContainsString('value="Google Maps" selected', $formHtml);
        $this->assertStringContainsString('Google-provided business fields are intentionally not prefilled or saved.', $formHtml);
        $this->assertStringNotContainsString('value="Strength House"', $formHtml);

        $leadData = [
            'name' => 'Aarav Verma',
            'gym_name' => 'Strength House',
            'phone' => '+91 98765 43210',
            'location' => 'Gomti Nagar, Lucknow',
            'source' => 'Google Maps',
            'priority' => 'warm',
            'status' => 'new',
            'notes' => 'Contact details independently verified by the sales rep.',
            'place_prospect_id' => $prospect->id,
        ];

        $this->actingAs($sales)->postJson(route('leads.store'), $leadData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('independent_contact_verified');
        $this->assertNull($prospect->fresh()->lead_id);
        $leadData['independent_contact_verified'] = '1';

        $response = $this->actingAs($sales)->postJson(route('leads.store'), $leadData)
            ->assertCreated()
            ->assertJsonPath('place_prospect.status', 'converted');
        $lead = Lead::query()->findOrFail($response->json('id'));
        $this->assertSame('Strength House', $lead->gym_name);
        $this->assertSame('+91 98765 43210', $lead->phone);
        $this->assertSame('Google Maps', $lead->source);
        $this->assertFalse(Schema::hasColumn('leads', 'independent_contact_verified'));
        $this->assertSame($lead->id, $prospect->fresh()->lead_id);
        $this->assertSame('converted', $prospect->fresh()->status);
        $this->actingAs($sales)->get(route('leads.index'))
            ->assertOk()
            ->assertSee('Google Maps')
            ->assertSee('Strength House')
            ->assertDontSee('Google Place prospect');
        $this->assertDatabaseHas('google_place_prospect_activities', [
            'prospect_id' => $prospect->id,
            'actor_id' => $sales->id,
            'event' => 'converted',
            'from_status' => 'qualified',
            'to_status' => 'converted',
        ]);
        $this->assertSame('ChIJGymProspectConvert01', $prospect->fresh()->place_id);

        $this->actingAs($sales)->postJson(route('leads.store'), $leadData)->assertStatus(409);
        $this->assertSame(1, Lead::query()->where('phone', '+91 98765 43210')->count());
    }

    public function test_admin_key_is_encrypted_and_connection_test_consumes_pro_not_enterprise_budget(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->manager()->create();
        Http::fake(function (ClientRequest $request) {
            if (str_contains($request->url(), 'maps.googleapis.com/maps/api/geocode/json')) {
                return Http::response($this->geocodeResponse(), 200);
            }
            if (str_contains($request->url(), 'places:searchText')) {
                return Http::response(['places' => []], 200);
            }

            return Http::response(['message' => 'Unexpected outbound request'], 500);
        });

        $this->actingAs($manager)->putJson(route('settings.google-places.update'), ['api_key' => self::API_KEY])->assertForbidden();
        $this->actingAs($admin)->putJson(route('settings.google-places.update'), ['api_key' => self::API_KEY])
            ->assertOk()
            ->assertJsonPath('configured', true)
            ->assertDontSee(self::API_KEY);
        $this->assertSame(self::API_KEY, WorkspaceSetting::secret('google_places_api_key'));
        $rawSecret = WorkspaceSetting::query()->where('setting_key', 'google_places_api_key')->firstOrFail()->getRawOriginal('setting_value');
        $this->assertNotSame(self::API_KEY, $rawSecret);

        $this->postJson(route('settings.google-places.test'))
            ->assertOk()
            ->assertJsonPath('usage.categories.text_search_pro.used', 1)
            ->assertJsonPath('usage.categories.place_details_enterprise.used', 0);
        $this->assertDatabaseHas('google_places_api_usage', ['sku' => 'geocoding', 'reserved_requests' => 1]);
        $this->assertDatabaseHas('google_places_api_usage', ['sku' => 'text_search_pro', 'reserved_requests' => 1]);
        $this->assertDatabaseMissing('google_places_api_usage', ['sku' => 'text_search_ids_only']);
    }

    public function test_default_monthly_caps_match_the_approved_india_tier_guardrails(): void
    {
        $this->assertSame(2, (int) config('google_places.max_pages'));
        $this->assertSame(20, (int) config('google_places.page_size'));
        $this->assertSame(25000, (int) config('google_places.monthly_caps.text_search_pro'));
        $this->assertSame(5000, (int) config('google_places.monthly_caps.place_details_enterprise'));
        $this->assertSame(25000, (int) config('google_places.monthly_caps.geocoding'));
    }

    public function test_monthly_pro_cap_is_a_hard_stop_before_google_is_called(): void
    {
        config(['google_places.monthly_caps.text_search_pro' => 1]);
        $usage = app(GooglePlacesUsageService::class);
        $this->assertSame(1, $usage->reserve('text_search_pro'));

        WorkspaceSetting::putSecret('google_places_api_key', self::API_KEY);
        Http::fake(fn () => Http::response(['places' => []], 200));
        try {
            app(GooglePlacesClient::class)->searchText(['textQuery' => 'gym', 'pageSize' => 1]);
            $this->fail('Expected the local Pro cap to stop the request.');
        } catch (GooglePlacesException $exception) {
            $this->assertSame(429, $exception->httpStatus);
            $this->assertStringContainsString('1/1', $exception->getMessage());
        }

        Http::assertNothingSent();
        $this->assertSame(1, $usage->count('text_search_pro'));
    }

    public function test_monthly_enterprise_details_cap_is_a_hard_stop_before_google_is_called(): void
    {
        config(['google_places.monthly_caps.place_details_enterprise' => 1]);
        $usage = app(GooglePlacesUsageService::class);
        $this->assertSame(1, $usage->reserve('place_details_enterprise'));
        WorkspaceSetting::putSecret('google_places_api_key', self::API_KEY);
        Http::fake(fn () => Http::response([], 200));

        try {
            app(GooglePlacesClient::class)->details('ChIJGymProspectLimit01');
            $this->fail('Expected the local Enterprise cap to stop the request.');
        } catch (GooglePlacesException $exception) {
            $this->assertSame(429, $exception->httpStatus);
            $this->assertStringContainsString('1/1', $exception->getMessage());
        }

        Http::assertNothingSent();
        $this->assertSame(1, $usage->count('place_details_enterprise'));
    }

    private function googlePlace(string $id, string $name, float $latitude, float $longitude): array
    {
        return [
            'id' => $id,
            'displayName' => ['text' => $name],
            'formattedAddress' => 'Gomti Nagar, Lucknow, India',
            'googleMapsUri' => 'https://maps.google.com/?cid=12345',
            'location' => ['latitude' => $latitude, 'longitude' => $longitude],
        ];
    }

    private function geocodeResponse(): array
    {
        return [
            'status' => 'OK',
            'results' => [[
                'geometry' => ['location' => ['lat' => 26.8467, 'lng' => 80.9462]],
            ]],
        ];
    }

    private function fakeSearchApis(array $firstPage, array $secondPage = [], ?string $nextPageToken = null): void
    {
        Http::fake(function (ClientRequest $request) use ($firstPage, $secondPage, $nextPageToken) {
            if (str_contains($request->url(), 'maps.googleapis.com/maps/api/geocode/json')) {
                return Http::response($this->geocodeResponse(), 200);
            }
            if (str_contains($request->url(), 'places:searchText')) {
                $pageToken = $request->data()['pageToken'] ?? null;
                if ($pageToken !== null) {
                    return Http::response(['places' => $secondPage], 200);
                }
                $response = ['places' => $firstPage];
                if ($nextPageToken !== null) {
                    $response['nextPageToken'] = $nextPageToken;
                }

                return Http::response($response, 200);
            }

            return Http::response(['message' => 'Unexpected outbound request'], 500);
        });
    }
}
