<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\PricingConfiguration;
use App\Models\Region;
use App\Services\PricingService;
use App\Support\MoneyFormatter;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PricingCalculationTest extends TestCase
{
    protected Region $region;
    protected Destination $dest1;
    protected Destination $dest2;
    protected PricingConfiguration $activePricing;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.google.server_key' => 'fake_google_server_key_for_testing']);

        $this->region = Region::create([
            'name' => 'Kawasan Tour Uji Pricing ' . uniqid(),
            'slug' => 'kawasan-tour-uji-pricing-' . uniqid(),
            'is_active' => true,
        ]);

        $this->dest1 = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Pantai Pandawa Uji Pricing',
            'slug' => 'pantai-pandawa-uji-pricing-' . uniqid(),
            'latitude' => -8.8451,
            'longitude' => 115.1872,
            'is_active' => true,
        ]);

        $this->dest2 = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Garuda Wisnu Kencana Uji Pricing',
            'slug' => 'gwk-uji-pricing-' . uniqid(),
            'latitude' => -8.8104,
            'longitude' => 115.1676,
            'is_active' => true,
        ]);

        // Nonaktifkan konfigurasi lama jika ada untuk isolasi test
        PricingConfiguration::query()->update(['is_active' => false]);

        // Buat konfigurasi tarif aktif untuk pengujian
        $this->activePricing = PricingConfiguration::create([
            'name' => 'Paket Sewa Mobil Avanza / Xpander',
            'base_price' => 150000,
            'price_per_kilometer' => 8000,
            'extra_passenger_price' => 40000,
            'parking_fee' => 0,
            'toll_fee' => 0,
            'night_service_fee' => 0,
            'minimum_price' => 300000,
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        $this->dest1->delete();
        $this->dest2->delete();
        $this->region->delete();
        $this->activePricing->delete();

        // Kembalikan status aktif tarif seeder default jika diperlukan
        PricingConfiguration::where('id', 1)->update(['is_active' => true]);

        parent::tearDown();
    }

    /**
     * Memverifikasi MoneyFormatter menghasilkan format Rupiah IDR standar tanpa desimal.
     */
    public function test_money_formatter_formats_idr_correctly(): void
    {
        $this->assertEquals('Rp872.000', MoneyFormatter::formatIdr(872000));
        $this->assertEquals('Rp150.000', MoneyFormatter::formatIdr('150000'));
        $this->assertEquals('Rp0', MoneyFormatter::formatIdr(0));
        $this->assertEquals('Rp0', MoneyFormatter::formatIdr(null));
    }

    /**
     * Memverifikasi kalkulasi rute dan biaya berhasil untuk 1 penumpang dengan tarif aktif.
     */
    public function test_estimate_calculation_success_with_single_passenger(): void
    {
        // Mock Google Routes API mengembalikan jarak 25 km (25000 meter)
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 25000,
                        'duration' => '2400s',
                        'polyline' => ['encodedPolyline' => 'fake_polyline_abc'],
                        'legs' => [
                            ['distanceMeters' => 25000, 'duration' => '2400s'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Bandara Ngurah Rai',
                'latitude' => -8.7481,
                'longitude' => 115.1672,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            'passenger_count' => 1,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Estimasi rute dan biaya berhasil dihitung.',
            'route' => [
                'distance_kilometers' => 25.0,
            ],
            'pricing' => [
                'currency' => 'IDR',
                'base_cost' => 150000,
                'distance_cost' => 200000, // 25 km * 8.000
                'passenger_cost' => 0,     // 1 penumpang = 0 ekstra
                'subtotal' => 350000,
                'minimum_price_applied' => false,
                'estimated_total' => 350000,
                'formatted_total' => 'Rp350.000',
            ],
        ]);
        $response->assertJsonStructure(['disclaimer']);
    }

    /**
     * Memverifikasi kalkulasi biaya untuk lebih dari 1 penumpang (penumpang ekstra dikenakan biaya).
     */
    public function test_estimate_calculation_with_extra_passengers(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 50000, // 50 km
                        'duration' => '4000s',
                        'polyline' => ['encodedPolyline' => 'fake_polyline'],
                        'legs' => [
                            ['distanceMeters' => 50000, 'duration' => '4000s'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // 4 penumpang: 1 termasuk dasar, 3 penumpang ekstra (3 x 40.000 = 120.000)
        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.7200,
                'longitude' => 115.1700,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            'passenger_count' => 4,
        ]);

        $response->assertOk();
        // base_cost: 150.000
        // distance_cost: 50 * 8.000 = 400.000
        // passenger_cost: 3 * 40.000 = 120.000
        // subtotal: 670.000
        $response->assertJson([
            'success' => true,
            'pricing' => [
                'base_cost' => 150000,
                'distance_cost' => 400000,
                'passenger_cost' => 120000,
                'subtotal' => 670000,
                'estimated_total' => 670000,
                'formatted_total' => 'Rp670.000',
            ],
        ]);
    }

    /**
     * Memverifikasi penerapan minimum price bila subtotal lebih kecil dari minimum_price.
     */
    public function test_minimum_price_is_applied_when_subtotal_is_lower(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 5000, // 5 km -> 5 * 8.000 = 40.000
                        'duration' => '600s',
                        'polyline' => ['encodedPolyline' => 'fake_poly'],
                        'legs' => [
                            ['distanceMeters' => 5000, 'duration' => '600s'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // subtotal = 150.000 (base) + 40.000 (dist) = 190.000 < minimum_price (300.000)
        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.7200,
                'longitude' => 115.1700,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            'passenger_count' => 1,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'pricing' => [
                'base_cost' => 150000,
                'distance_cost' => 40000,
                'subtotal' => 190000,
                'minimum_price_applied' => true,
                'minimum_price' => 300000,
                'estimated_total' => 300000,
                'formatted_total' => 'Rp300.000',
            ],
        ]);
    }

    /**
     * Memverifikasi pembulatan harga (ceil ke ribuan rupiah terdekat).
     */
    public function test_pricing_rounding_ceil_to_thousands(): void
    {
        $pricingService = new PricingService();

        // Misal jarak 12.35 km:
        // base: 150.000
        // distance: 12.35 * 8.000 = 98.800
        // total: 248.800 < minimum 300.000 -> 300.000
        // Jika minimum price diabaikan/diuji langsung:
        // misal jarak 35.125 km:
        // distance: 35.125 * 8.000 = 281.000
        // base: 150.000 -> subtotal = 431.000
        // Jika kita coba dengan pecahan:
        $result = $pricingService->calculate(35.123, 1);
        $this->assertEquals(0, $result['estimated_total'] % 1000);
        $this->assertGreaterThanOrEqual($result['subtotal'], $result['estimated_total']);
    }

    /**
     * Memverifikasi endpoint menolak jumlah penumpang nol atau negatif.
     */
    public function test_validation_fails_when_passenger_count_is_invalid(): void
    {
        // Test penumpang nol
        $response1 = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            'passenger_count' => 0,
        ]);
        $response1->assertStatus(422);
        $response1->assertJsonValidationErrors(['passenger_count']);

        // Test penumpang negatif
        $response2 = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            'passenger_count' => -2,
        ]);
        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors(['passenger_count']);

        // Test penumpang melebihi kapasitas max 12
        $response3 = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            'passenger_count' => 15,
        ]);
        $response3->assertStatus(422);
        $response3->assertJsonValidationErrors(['passenger_count']);
    }

    /**
     * Memverifikasi penanganan galat jika tidak ada tarif aktif di database.
     */
    public function test_estimate_fails_gracefully_when_no_active_pricing_configuration(): void
    {
        PricingConfiguration::query()->update(['is_active' => false]);

        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 25000,
                        'duration' => '2400s',
                        'polyline' => ['encodedPolyline' => 'fake_poly'],
                        'legs' => [
                            ['distanceMeters' => 25000, 'duration' => '2400s'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            'passenger_count' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Estimasi biaya belum dapat dihitung karena tarif belum dikonfigurasi.',
        ]);
    }

    /**
     * Memverifikasi route alias calculator.estimate berfungsi identik.
     */
    public function test_calculator_estimate_alias_route_works(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 30000,
                        'duration' => '3000s',
                        'polyline' => ['encodedPolyline' => 'fake_poly'],
                        'legs' => [
                            ['distanceMeters' => 30000, 'duration' => '3000s'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('calculator.estimate'), [
            'pickup' => [
                'name' => 'Sanur',
                'latitude' => -8.68,
                'longitude' => 115.26,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            'passenger_count' => 2,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'pricing' => [
                'currency' => 'IDR',
                'passenger_cost' => 40000,
            ],
        ]);
    }
}
