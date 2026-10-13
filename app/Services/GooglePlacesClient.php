<?php

namespace App\Services;

use App\Exceptions\GooglePlacesException;
use App\Models\WorkspaceSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GooglePlacesClient
{
    private const GEOCODING_ENDPOINT = 'https://maps.googleapis.com/maps/api/geocode/json';
    private const PLACES_SEARCH_ENDPOINT = 'https://places.googleapis.com/v1/places:searchText';
    private const PLACE_DETAILS_ENDPOINT = 'https://places.googleapis.com/v1/places/';

    private const SEARCH_FIELD_MASK = 'places.id,places.displayName,places.formattedAddress,places.googleMapsUri,places.location,nextPageToken';
    private const DETAILS_FIELD_MASK = 'id,displayName,formattedAddress,nationalPhoneNumber,userRatingCount,googleMapsUri';

    public function __construct(private readonly GooglePlacesUsageService $usage)
    {
    }

    public function geocode(string $address): array
    {
        $key = $this->apiKey();
        $this->usage->reserve('geocoding');

        // Do not call throw() or log the request: the Geocoding API key is sent as a query parameter.
        $response = $this->safeRequest(fn () => Http::acceptJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->get(self::GEOCODING_ENDPOINT, [
                'address' => $address,
                'language' => 'en',
                'region' => config('google_places.region', 'IN'),
                'key' => $key,
            ]));

        if (! $response->successful()) {
            throw $this->requestException($response);
        }

        $payload = $response->json();
        $status = $payload['status'] ?? 'UNKNOWN_ERROR';
        if ($status === 'ZERO_RESULTS') {
            throw new GooglePlacesException('No matching location was found. Try a more specific city, neighbourhood, or address.', 422);
        }
        if ($status === 'OVER_QUERY_LIMIT') {
            throw new GooglePlacesException('Google location lookup is rate-limited. Try again later or review the Cloud quota.', 429);
        }
        if ($status !== 'OK') {
            throw new GooglePlacesException('Google could not resolve this location. Check the API key, billing, and Geocoding API access.', 502);
        }

        $location = $payload['results'][0]['geometry']['location'] ?? null;
        if (! is_array($location) || ! is_numeric($location['lat'] ?? null) || ! is_numeric($location['lng'] ?? null)) {
            throw new GooglePlacesException('Google returned an incomplete location result. Try a more specific address.', 502);
        }

        return ['lat' => (float) $location['lat'], 'lng' => (float) $location['lng']];
    }

    public function searchText(array $body): array
    {
        return $this->sendTextSearch($body, self::SEARCH_FIELD_MASK, 'text_search_pro');
    }

    public function details(string $placeId): array
    {
        $key = $this->apiKey();
        $this->usage->reserve('place_details_enterprise');
        $url = self::PLACE_DETAILS_ENDPOINT.rawurlencode($placeId);

        $response = $this->safeRequest(fn () => Http::acceptJson()
            ->withHeaders([
                'X-Goog-Api-Key' => $key,
                'X-Goog-FieldMask' => self::DETAILS_FIELD_MASK,
            ])
            ->connectTimeout(5)
            ->timeout(15)
            ->get($url));

        if (! $response->successful()) {
            throw $this->requestException($response);
        }

        $payload = $response->json();
        if (! is_array($payload) || ! is_string($payload['id'] ?? null)) {
            throw new GooglePlacesException('Google returned incomplete place details. Try again later.', 502);
        }

        return $this->presentablePlace($payload);
    }

    public function presentablePlace(array $place): array
    {
        return $this->filterPlace($place, includeEnterpriseFields: true);
    }

    public function presentableSearchResult(array $place): array
    {
        return $this->filterPlace($place, includeEnterpriseFields: false);
    }

    public function verifyConnection(): void
    {
        $center = $this->geocode('Lucknow, Uttar Pradesh, India');
        $this->sendTextSearch([
            'textQuery' => 'gym',
            'pageSize' => 1,
            'locationBias' => [
                'circle' => [
                    'center' => ['latitude' => $center['lat'], 'longitude' => $center['lng']],
                    'radius' => 1000,
                ],
            ],
        ], self::SEARCH_FIELD_MASK, 'text_search_pro');
    }

    private function sendTextSearch(array $body, string $fieldMask, string $sku): array
    {
        $key = $this->apiKey();
        $this->usage->reserve($sku);

        $response = $this->safeRequest(fn () => Http::acceptJson()
            ->withHeaders([
                'X-Goog-Api-Key' => $key,
                'X-Goog-FieldMask' => $fieldMask,
            ])
            ->connectTimeout(5)
            ->timeout(15)
            ->post(self::PLACES_SEARCH_ENDPOINT, $body));

        if (! $response->successful()) {
            throw $this->requestException($response);
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            throw new GooglePlacesException('Google returned an unreadable search response. Try again later.', 502);
        }

        return [
            'places' => array_values(array_filter($payload['places'] ?? [], 'is_array')),
            'next_page_token' => is_string($payload['nextPageToken'] ?? null) ? $payload['nextPageToken'] : null,
        ];
    }

    private function filterPlace(array $place, bool $includeEnterpriseFields): array
    {
        $name = $place['displayName']['text'] ?? null;
        $mapUrl = $place['googleMapsUri'] ?? null;
        $filtered = [
            'place_id' => (string) ($place['id'] ?? ''),
            'name' => is_string($name) ? mb_substr($name, 0, 200) : 'Unnamed place',
            'address' => is_string($place['formattedAddress'] ?? null) ? mb_substr($place['formattedAddress'], 0, 500) : null,
            'maps_url' => $this->safeMapsUrl($mapUrl),
        ];

        if ($includeEnterpriseFields) {
            $filtered['phone'] = is_string($place['nationalPhoneNumber'] ?? null)
                ? mb_substr($place['nationalPhoneNumber'], 0, 40)
                : null;
            $filtered['review_count'] = is_numeric($place['userRatingCount'] ?? null)
                ? max(0, (int) $place['userRatingCount'])
                : null;
        }

        return $filtered;
    }

    private function safeMapsUrl(mixed $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        $parts = parse_url($url);
        if (($parts['scheme'] ?? null) !== 'https' || ! in_array(strtolower($parts['host'] ?? ''), ['google.com', 'www.google.com', 'maps.google.com'], true)) {
            return null;
        }

        return $url;
    }

    private function apiKey(): string
    {
        $key = WorkspaceSetting::secret('google_places_api_key');
        if (! $key) {
            throw new GooglePlacesException('Google Places is not configured. Ask an Admin to add a key in Settings.', 503);
        }

        return $key;
    }

    private function safeRequest(callable $request): Response
    {
        try {
            return $request();
        } catch (ConnectionException) {
            throw new GooglePlacesException('Google API did not respond before the timeout. The request was not retried; check usage and try again.', 504);
        }
    }

    private function requestException(Response $response): GooglePlacesException
    {
        if (in_array($response->status(), [401, 403], true)) {
            return new GooglePlacesException('Google rejected the API key. Check that Places API (New) and Geocoding API are enabled and the server key restrictions are correct.', 502);
        }
        if ($response->status() === 404) {
            return new GooglePlacesException('Google could not find that place. Refresh the live details and try again.', 404);
        }
        if (in_array($response->status(), [429, 503], true)) {
            return new GooglePlacesException('Google Places is temporarily rate-limited or unavailable. Try again later.', 429);
        }

        return new GooglePlacesException('Google Places did not complete the request. Check the key, enabled APIs, quota, and billing configuration.', 502);
    }
}
