<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RouteCalculationTest extends TestCase
{
    protected Region $region;
    protected Destination $dest1;
    protected Destination $dest2;
    protected Destination $dest3;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.google.server_key' => 'fake_google_server_key_for_testing']);

        $this->region = Region::create([
            'name' => 'Kawasan Tour Uji ' . uniqid(),
            'slug' => 'kawasan-tour-uji-' . uniqid(),
            'is_active' => true,
        ]);

        $this->dest1 = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Pantai Kuta Uji Rute',
            'slug' => 'pantai-kuta-uji-rute-' . uniqid(),
            'latitude' => -8.7180,
            'longitude' => 115.1690,
            'is_active' => true,
        ]);

        $this->dest2 = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Pura Tanah Lot Uji Rute',
            'slug' => 'pura-tanah-lot-uji-rute-' . uniqid(),
            'latitude' => -8.6212,
            'longitude' => 115.0868,
            'is_active' => true,
        ]);

        $this->dest3 = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Pura Uluwatu Uji Rute',
            'slug' => 'pura-uluwatu-uji-rute-' . uniqid(),
            'latitude' => -8.8291,
            'longitude' => 115.0849,
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        $this->dest1->delete();
        $this->dest2->delete();
        $this->dest3->delete();
        $this->region->delete();

        parent::tearDown();
    }

    /**
     * Memverifikasi kalkulasi rute berhasil untuk 1 titik jemput dan 1 tempat wisata.
     */
    public function test_route_calculation_success_with_single_destination(): void
    {
        // Mock Google Routes API response
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 25400,
                        'duration' => '2400s',
                        'polyline' => [
                            'encodedPolyline' => 'fake_encoded_polyline_string_abc123',
                        ],
                        'legs' => [
                            [
                                'distanceMeters' => 25400,
                                'duration' => '2400s',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Pastikan dummy server key tersedia saat test
        putenv('GOOGLE_MAPS_SERVER_KEY=fake_google_server_key_for_testing');

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Bandara Ngurah Rai',
                'latitude' => -8.7481,
                'longitude' => 115.1672,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'route' => [
                'distance_meters' => 25400,
                'distance_kilometers' => 25.4,
                'duration_seconds' => 2400,
                'duration_text' => '40 menit',
                'encoded_polyline' => 'fake_encoded_polyline_string_abc123',
                'legs' => [
                    [
                        'from' => ['name' => 'Bandara Ngurah Rai'],
                        'to' => ['name' => 'Pantai Kuta Uji Rute'],
                        'distance_kilometers' => 25.4,
                        'duration_text' => '40 menit',
                    ],
                ],
            ],
        ]);

        // Verifikasi tidak ada API key yang bocor di respon
        $this->assertStringNotContainsString('fake_google_server_key_for_testing', $response->getContent());
    }

    /**
     * Memverifikasi kalkulasi rute berhasil untuk multiple destinasi dengan pembagian intermediate legs.
     */
    public function test_route_calculation_success_with_multiple_destinations(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 65000,
                        'duration' => '5400s',
                        'polyline' => [
                            'encodedPolyline' => 'fake_multi_polyline',
                        ],
                        'legs' => [
                            [
                                'distanceMeters' => 15000,
                                'duration' => '1800s',
                            ],
                            [
                                'distanceMeters' => 25000,
                                'duration' => '2100s',
                            ],
                            [
                                'distanceMeters' => 25000,
                                'duration' => '1500s',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        putenv('GOOGLE_MAPS_SERVER_KEY=fake_google_server_key_for_testing');

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Bandara Ngurah Rai',
                'latitude' => -8.7481,
                'longitude' => 115.1672,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
                ['id' => $this->dest2->id],
                ['id' => $this->dest3->id],
            ],
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'route' => [
                'distance_kilometers' => 65.0,
                'duration_seconds' => 5400,
                'duration_text' => '1 jam 30 menit',
            ],
        ]);

        $data = $response->json('route.legs');
        $this->assertCount(3, $data);
        $this->assertEquals('Pantai Kuta Uji Rute', $data[0]['to']['name']);
        $this->assertEquals('Pura Tanah Lot Uji Rute', $data[1]['to']['name']);
        $this->assertEquals('Pura Uluwatu Uji Rute', $data[2]['to']['name']);
    }

    /**
     * Memverifikasi request ditolak jika destinasi melebihi batas maksimal 5.
     */
    public function test_route_calculation_fails_when_destinations_exceed_five(): void
    {
        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
                ['id' => 4],
                ['id' => 5],
                ['id' => 6],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['destinations']);
    }

    /**
     * Memverifikasi request ditolak jika ada destinasi yang duplikat.
     */
    public function test_route_calculation_fails_on_duplicate_destinations(): void
    {
        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
                ['id' => $this->dest1->id],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['destinations']);
    }

    /**
     * Memverifikasi request ditolak jika salah satu destinasi nonaktif.
     */
    public function test_route_calculation_fails_when_destination_inactive(): void
    {
        $inactiveDest = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Wisata Tutup Nonaktif',
            'slug' => 'wisata-tutup-nonaktif-' . uniqid(),
            'latitude' => -8.50,
            'longitude' => 115.20,
            'is_active' => false,
        ]);

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $inactiveDest->id],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['destinations']);

        $inactiveDest->delete();
    }

    /**
     * Memverifikasi request ditolak jika koordinat titik jemput berada di luar rentang valid.
     */
    public function test_route_calculation_fails_on_invalid_pickup_coordinates(): void
    {
        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Titik Salah',
                'latitude' => 120.00, // Di luar rentang -90 s/d 90
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['pickup.latitude']);
    }

    /**
     * Memverifikasi penanganan error aman ketika Google Routes API mengembalikan error.
     */
    public function test_route_calculation_fails_gracefully_on_google_api_error(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'error' => [
                    'code' => 403,
                    'message' => 'API keys with referrers cannot be used with this API.',
                ],
            ], 403),
        ]);

        putenv('GOOGLE_MAPS_SERVER_KEY=dummy_error_key');

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Rute belum dapat dihitung dari server peta. Silakan coba sesaat lagi.',
        ]);
    }

    /**
     * Memverifikasi penanganan error ketika Google Server Key belum dikonfigurasi.
     */
    public function test_route_calculation_fails_when_server_key_missing(): void
    {
        config(['services.google.server_key' => null]);
        putenv('GOOGLE_MAPS_SERVER_KEY=');
        putenv('GOOGLE_ROUTES_API_KEY=');
        putenv('GOOGLE_MAPS_API_KEY=');

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => [
                'name' => 'Kuta',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Layanan peta sedang tidak tersedia. Konfigurasi server belum lengkap.',
        ]);
    }
}
