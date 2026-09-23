<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Bisnis Tour Bali
    |--------------------------------------------------------------------------
    */
    'brand_name' => env('APP_NAME', 'Jumu Bali Tour'),
    'whatsapp_number' => env('WHATSAPP_PHONE_NUMBER', '6281234567890'),
    'tagline' => 'Eksplorasi Keindahan Bali dengan Kenyamanan Terbaik',
    
    // Titik jemput default populer di Bali untuk fallback kalkulator
    'default_pickup_points' => [
        ['name' => 'Bandara Internasional I Gusti Ngurah Rai (DPS)', 'lat' => -8.748167, 'lng' => 115.167175],
        ['name' => 'Kuta / Legian', 'lat' => -8.7185, 'lng' => 115.1686],
        ['name' => 'Seminyak', 'lat' => -8.6913, 'lng' => 115.1582],
        ['name' => 'Sanur', 'lat' => -8.6882, 'lng' => 115.2625],
        ['name' => 'Ubud Center', 'lat' => -8.5069, 'lng' => 115.2625],
        ['name' => 'Canggu', 'lat' => -8.6478, 'lng' => 115.1385],
    ],
    
    // Batas maksimal destinasi per trip/hari
    'max_destinations_per_trip' => 5,
];
