<?php

namespace App\Services;

use App\Exceptions\GooglePlacesException;
use App\Models\GooglePlaceProspect;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class GooglePlacesSearchService
{
    public function __construct(private readonly GooglePlacesClient $client)
    {
    }

    public function search(string $location, float $radiusKm, User $actor, ?string $pageState = null): array
    {
        $queryHash = hash('sha256', mb_strtolower(trim($location)).'|'.number_format($radiusKm, 3, '.', ''));
        if ($pageState) {
            $state = $this->decodeState($pageState, $actor, $queryHash);
            $center = $state['center'];
            $page = $state['page'];
            $pageToken = $state['page_token'];
        } else {
            $center = $this->client->geocode($location);
            $page = 1;
            $pageToken = null;
        }

        $body = [
            'textQuery' => 'gym',
            'includedType' => 'gym',
            'strictTypeFiltering' => true,
            'pageSize' => (int) config('google_places.page_size', 20),
            'languageCode' => 'en',
            'regionCode' => config('google_places.region', 'IN'),
            'locationRestriction' => ['rectangle' => $this->boundingRectangle($center, $radiusKm)],
        ];
        if ($pageToken) {
            $body['pageToken'] = $pageToken;
        }

        $payload = $this->client->searchText($body);
        $ids = [];
        $places = [];
        foreach ($payload['places'] as $place) {
            $locationData = $place['location'] ?? null;
            $placeId = $place['id'] ?? null;
            if (! is_string($placeId) || $placeId === '' || ! is_array($locationData)) {
                continue;
            }
            $lat = $locationData['latitude'] ?? null;
            $lng = $locationData['longitude'] ?? null;
            if (! is_numeric($lat) || ! is_numeric($lng) || $this->distanceKm($center, ['lat' => (float) $lat, 'lng' => (float) $lng]) > $radiusKm) {
                continue;
            }
            if (isset($ids[$placeId])) {
                continue;
            }
            $ids[$placeId] = true;
            $presented = $this->client->presentableSearchResult($place);
            $presented['selection_token'] = $this->selectionToken($placeId, $actor);
            $places[] = $presented;
        }

        $existingIds = $ids
            ? GooglePlaceProspect::query()->whereIn('place_id', array_keys($ids))->pluck('place_id')->all()
            : [];
        $existingLookup = array_fill_keys($existingIds, true);
        foreach ($places as &$place) {
            $place['already_added'] = isset($existingLookup[$place['place_id']]);
        }
        unset($place);

        $nextPageToken = $payload['next_page_token'];
        $maxPages = (int) config('google_places.max_pages', 2);
        $nextState = $nextPageToken && $page < $maxPages
            ? $this->encodeState([
                'user_id' => $actor->id,
                'query_hash' => $queryHash,
                'center' => $center,
                'page' => $page + 1,
                'page_token' => $nextPageToken,
                'expires_at' => now()->addMinutes((int) config('google_places.page_state_ttl_minutes', 30))->timestamp,
            ])
            : null;

        return [
            'results' => $places,
            'next_page_state' => $nextState,
            'page' => $page,
            'max_pages' => $maxPages,
            'radius_km' => $radiusKm,
        ];
    }

    public function resolveSelectionToken(string $token, User $actor): string
    {
        try {
            $payload = json_decode(Crypt::decryptString($token), true, 8, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new GooglePlacesException('One or more search results expired. Search again before assigning prospects.', 422);
        }

        if (($payload['user_id'] ?? null) !== $actor->id
            || ! is_int($payload['expires_at'] ?? null)
            || $payload['expires_at'] < now()->timestamp
            || ! $this->validPlaceId($payload['place_id'] ?? null)) {
            throw new GooglePlacesException('One or more search results expired. Search again before assigning prospects.', 422);
        }

        return $payload['place_id'];
    }

    private function decodeState(string $token, User $actor, string $queryHash): array
    {
        try {
            $state = json_decode(Crypt::decryptString($token), true, 12, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new GooglePlacesException('This search session expired. Run the search again.', 422);
        }

        $center = $state['center'] ?? null;
        if (($state['user_id'] ?? null) !== $actor->id
            || ($state['query_hash'] ?? null) !== $queryHash
            || ! is_int($state['expires_at'] ?? null)
            || $state['expires_at'] < now()->timestamp
            || ! is_array($center)
            || ! is_numeric($center['lat'] ?? null)
            || ! is_numeric($center['lng'] ?? null)
            || ! is_int($state['page'] ?? null)
            || $state['page'] < 2
            || $state['page'] > (int) config('google_places.max_pages', 2)
            || ! is_string($state['page_token'] ?? null)
            || $state['page_token'] === '') {
            throw new GooglePlacesException('This search session expired or does not match the current search. Run the search again.', 422);
        }

        return $state;
    }

    private function selectionToken(string $placeId, User $actor): string
    {
        return Crypt::encryptString(json_encode([
            'user_id' => $actor->id,
            'place_id' => $placeId,
            'expires_at' => now()->addMinutes((int) config('google_places.page_state_ttl_minutes', 30))->timestamp,
        ], JSON_THROW_ON_ERROR));
    }

    private function encodeState(array $state): string
    {
        return Crypt::encryptString(json_encode($state, JSON_THROW_ON_ERROR));
    }

    private function validPlaceId(mixed $placeId): bool
    {
        return is_string($placeId)
            && strlen($placeId) >= 8
            && strlen($placeId) <= 255
            && preg_match('/^[A-Za-z0-9_-]+$/', $placeId) === 1;
    }

    private function boundingRectangle(array $center, float $radiusKm): array
    {
        $latitudeDelta = $radiusKm / 111.32;
        $cosLatitude = cos(deg2rad($center['lat']));
        $longitudeDelta = abs($cosLatitude) < 0.000001 ? 180 : min(180, $radiusKm / (111.32 * abs($cosLatitude)));

        return [
            'low' => [
                'latitude' => max(-90, $center['lat'] - $latitudeDelta),
                'longitude' => max(-180, $center['lng'] - $longitudeDelta),
            ],
            'high' => [
                'latitude' => min(90, $center['lat'] + $latitudeDelta),
                'longitude' => min(180, $center['lng'] + $longitudeDelta),
            ],
        ];
    }

    private function distanceKm(array $a, array $b): float
    {
        $latitudeDelta = deg2rad($b['lat'] - $a['lat']);
        $longitudeDelta = deg2rad($b['lng'] - $a['lng']);
        $originLatitude = deg2rad($a['lat']);
        $destinationLatitude = deg2rad($b['lat']);
        $haversine = sin($latitudeDelta / 2) ** 2
            + cos($originLatitude) * cos($destinationLatitude) * sin($longitudeDelta / 2) ** 2;

        return 6371.0088 * 2 * asin(min(1, sqrt(max(0, $haversine))));
    }
}
