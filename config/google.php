<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Maps API Keys
    |--------------------------------------------------------------------------
    |
    | Client Key: Untuk Google Maps JavaScript API di browser client.
    | Dibatasi dengan HTTP Referrer di Google Cloud Console.
    |
    | Server Key: Untuk Google Routes API di server backend (ComputeRoutes).
    | Dibatasi dengan IP restriction di Google Cloud Console.
    |
    */
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    'routes_api_key' => env('GOOGLE_ROUTES_API_KEY', ''),
];
