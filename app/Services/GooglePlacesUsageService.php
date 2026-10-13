<?php

namespace App\Services;

use App\Exceptions\GooglePlacesException;
use App\Models\GooglePlacesApiUsage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class GooglePlacesUsageService
{
    public function billingMonth(): string
    {
        return CarbonImmutable::now(config('google_places.usage_timezone', 'America/Los_Angeles'))->format('Y-m');
    }

    /** Reserve before the outbound request so concurrent workers cannot exceed this app's cap. */
    public function reserve(string $sku): int
    {
        $cap = (int) config("google_places.monthly_caps.{$sku}", 0);
        abort_if($cap < 1, 500, 'A Google Places usage cap is not configured.');

        $month = $this->billingMonth();
        $now = now();
        DB::table('google_places_api_usage')->insertOrIgnore([
            'billing_month' => $month,
            'sku' => $sku,
            'reserved_requests' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $reserved = DB::table('google_places_api_usage')
            ->where('billing_month', $month)
            ->where('sku', $sku)
            ->where('reserved_requests', '<', $cap)
            ->increment('reserved_requests', 1, ['updated_at' => $now]);

        if ($reserved === 0) {
            $used = $this->count($sku, $month);
            throw new GooglePlacesException(
                "GymXBook paused this Google API category at its {$cap}-request monthly safety limit ({$used}/{$cap}). It will reset at the next Google billing month.",
                429
            );
        }

        return $this->count($sku, $month);
    }

    public function count(string $sku, ?string $month = null): int
    {
        return (int) GooglePlacesApiUsage::query()
            ->where('billing_month', $month ?? $this->billingMonth())
            ->where('sku', $sku)
            ->value('reserved_requests');
    }

    public function overview(): array
    {
        $month = $this->billingMonth();
        $counts = GooglePlacesApiUsage::query()
            ->where('billing_month', $month)
            ->pluck('reserved_requests', 'sku');
        $categories = [
            'geocoding' => ['label' => 'Location lookups', 'tier' => 'Geocoding Essentials', 'cap' => (int) config('google_places.monthly_caps.geocoding')],
            'text_search_pro' => ['label' => 'Gym search pages', 'tier' => 'Places Text Search · Pro fields', 'cap' => (int) config('google_places.monthly_caps.text_search_pro')],
            'place_details_enterprise' => ['label' => 'Live prospect detail lookups', 'tier' => 'Places Details · Enterprise fields', 'cap' => (int) config('google_places.monthly_caps.place_details_enterprise')],
        ];

        foreach ($categories as $sku => &$category) {
            $category['used'] = (int) ($counts[$sku] ?? 0);
            $category['remaining'] = max(0, $category['cap'] - $category['used']);
            $category['percent'] = $category['cap'] > 0
                ? min(100, (int) round(($category['used'] / $category['cap']) * 100))
                : 0;
        }
        unset($category);

        return [
            'month' => $month,
            'timezone' => config('google_places.usage_timezone', 'America/Los_Angeles'),
            'categories' => $categories,
        ];
    }
}
