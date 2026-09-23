<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\PricingConfiguration;
use App\Models\Region;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleRoutesApiMockTest extends TestCase
{
    protected Region $region;
    protected Destination $dest1;
    protected Destination $dest2;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.google.server_key' => 'fake_server_key_mock_testing']);

        $this->region = Region::create([
            'name' => 'Kawasan Routes Mock ' . uniqid(),
            'slug' => 'kawasan-routes-mock-' . uniqid(),
            'is_active' => true,
        ]);

        $this->dest1 = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Tirta Empul Mock',
            'slug' => 'tirta-empul-mock-' . uniqid(),
            'latitude' => -8.4149,
            'longitude' => 115.3150,
            'is_active' => true,
        ]);

        $this->dest2 = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Kintamani Batur Mock',
            'slug' => 'kintamani-batur-mock-' . uniqid(),
            'latitude' => -8.2435,
            'longitude' => 115.3340,
            'is_active' => true,
        ]);

        PricingConfiguration::where('id', 1)->update(['is_active' => true]);
    }

    protected function tearDown(): void
    {
        $this->dest1->delete();
        $this->dest2->delete();
        $this->region->delete();
        parent::tearDown();
    }

    /**
     * Memverifikasi Google Routes API fake response diproses dengan benar dan field mask terkirim.
     */
    public function test_google_routes_api_successful_fake_response(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => function ($request) {
                // Verifikasi header dan field mask
                $this->assertEquals('fake_server_key_mock_testing', $request->header('X-Goog-Api-Key')[0]);
                $this->assertStringContainsString('routes.distanceMeters', $request->header('X-Goog-FieldMask')[0]);

                // Verifikasi payload structure
                $payload = json_decode($request->body(), true);
                $this->assertArrayHasKey('origin', $payload);
                $this->assertArrayHasKey('destination', $payload);

                return Http::response([
                    'routes' => [
                        [
                            'distanceMeters' => 45000,
                            'duration' => '3600s',
                            'polyline' => ['encodedPolyline' => 'polyline_sample_123'],
                            'legs' => [
                                ['distanceMeters' => 20000, 'duration' => '1500s'],
                                ['distanceMeters' => 25000, 'duration' => '2100s'],
                            ],
                        ],
                    ],
                ], 200);
            },
        ]);

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => ['name' => 'Ubud', 'latitude' => -8.5069, 'longitude' => 115.2625],
            'destinations' => [
                ['id' => $this->dest1->id],
                ['id' => $this->dest2->id],
            ],
            'passenger_count' => 2,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'route' => [
                'distance_meters' => 45000,
                'distance_kilometers' => 45,
                'duration_seconds' => 3600,
                'duration_text' => '1 jam',
                'encoded_polyline' => 'polyline_sample_123',
            ],
        ]);

        // Verifikasi tidak ada API key bocor di response
        $this->assertStringNotContainsString('fake_server_key_mock_testing', $response->getContent());
    }

    /**
     * Memverifikasi respon Google Routes API tanpa polyline tetap ditangani dengan aman.
     */
    public function test_google_routes_api_handles_null_polyline_gracefully(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 15000,
                        'duration' => '1200s',
                        'legs' => [
                            ['distanceMeters' => 15000, 'duration' => '1200s'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => ['name' => 'Sanur', 'latitude' => -8.68, 'longitude' => 115.26],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
        ]);

        $response->assertOk();
        $this->assertNull($response->json('route.encoded_polyline'));
        $this->assertEquals(15.0, $response->json('route.distance_kilometers'));
    }

    /**
     * Memverifikasi penanganan ketika Google Routes API mengembalikan HTTP 400 Bad Request.
     */
    public function test_google_routes_api_handles_400_error(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'error' => ['code' => 400, 'message' => 'Invalid intermediate location.'],
            ], 400),
        ]);

        $response = $this->postJson(route('calculator.route'), [
            'pickup' => ['name' => 'Kuta', 'latitude' => -8.72, 'longitude' => 115.17],
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
}
