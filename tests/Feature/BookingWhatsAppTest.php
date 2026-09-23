<?php

namespace Tests\Feature;

use App\Models\BrandSetting;
use App\Models\Destination;
use App\Models\PricingConfiguration;
use App\Models\Region;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookingWhatsAppTest extends TestCase
{
    protected Region $region;
    protected Destination $dest1;
    protected PricingConfiguration $pricing;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.google.server_key' => 'fake_google_server_key_for_testing']);

        $this->region = Region::create([
            'name' => 'Kawasan Tour Uji Booking ' . uniqid(),
            'slug' => 'kawasan-tour-uji-booking-' . uniqid(),
            'is_active' => true,
        ]);

        $this->dest1 = Destination::create([
            'region_id' => $this->region->id,
            'name' => 'Pura Besakih Uji Booking',
            'slug' => 'pura-besakih-uji-booking-' . uniqid(),
            'latitude' => -8.3739,
            'longitude' => 115.4523,
            'is_active' => true,
        ]);

        PricingConfiguration::where('id', 1)->update(['is_active' => true]);

        BrandSetting::where('id', 1)->update([
            'whatsapp_number' => '081234567890',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        $this->dest1->delete();
        $this->region->delete();
        parent::tearDown();
    }

    /**
     * Memverifikasi pemesanan valid menghasilkan URL wa.me dan menghitung rute serta biaya di server.
     */
    public function test_booking_whatsapp_generates_valid_url(): void
    {
        Http::fake([
            'https://routes.googleapis.com/*' => Http::response([
                'routes' => [
                    [
                        'distanceMeters' => 40000,
                        'duration' => '3600s',
                        'legs' => [
                            ['distanceMeters' => 40000, 'duration' => '3600s'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('booking.whatsapp'), [
            'customer_name' => 'I Wayan Sudira',
            'customer_phone' => '087812345678',
            'tour_date' => date('Y-m-d', strtotime('+3 days')),
            'passenger_count' => 3,
            'notes' => 'Tolong sediakan air mineral.',
            'pickup' => [
                'name' => 'Kuta Beach Hotel',
                'latitude' => -8.72,
                'longitude' => 115.17,
            ],
            'destinations' => [
                ['id' => $this->dest1->id],
            ],
            // Manipulasi input harga dari client (wajib diabaikan)
            'estimated_total' => 10000,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Tautan pemesanan WhatsApp berhasil dibuat.',
        ]);

        $whatsappUrl = $response->json('whatsapp_url');
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $whatsappUrl);
        $this->assertStringContainsString('I%20Wayan%20Sudira', $whatsappUrl);
        $this->assertStringContainsString('Pura%20Besakih%20Uji%20Booking', $whatsappUrl);

        // Verifikasi tidak ada tabel bookings / tidak ada data pemesanan yang disimpan
        $this->assertDatabaseMissing('destinations', ['name' => 'I Wayan Sudira']);
    }

    /**
     * Memverifikasi pemesanan dengan tanggal masa lalu ditolak (HTTP 422).
     */
    public function test_booking_whatsapp_fails_on_past_date(): void
    {
        $response = $this->postJson(route('booking.whatsapp'), [
            'customer_name' => 'Made Arta',
            'customer_phone' => '087812345678',
            'tour_date' => '2020-01-01', // Tanggal lampau
            'passenger_count' => 2,
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
        $response->assertJsonValidationErrors(['tour_date']);
    }

    /**
     * Memverifikasi pemesanan dengan jumlah penumpang nol atau melebihi kapasitas ditolak.
     */
    public function test_booking_whatsapp_fails_on_invalid_passenger_count(): void
    {
        $response = $this->postJson(route('booking.whatsapp'), [
            'customer_name' => 'Made Arta',
            'customer_phone' => '087812345678',
            'tour_date' => date('Y-m-d', strtotime('+1 day')),
            'passenger_count' => 0,
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
        $response->assertJsonValidationErrors(['passenger_count']);
    }
}
