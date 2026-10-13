<?php

return [
    /*
    | The application guardrails intentionally sit below the current India free
    | usage caps. These counters only cover calls made by this GymXBook instance;
    | the Google billing account can include usage from other projects.
    */
    'usage_timezone' => 'America/Los_Angeles',
    'page_state_ttl_minutes' => 30,
    'page_size' => 20,
    'max_pages' => 2,
    'radius_default_km' => 5,
    'radius_min_km' => 1,
    'radius_max_km' => 50,
    'region' => 'IN',
    'monthly_caps' => [
        'geocoding' => 25000,
        'text_search_pro' => 25000,
        'place_details_enterprise' => 5000,
    ],
];
