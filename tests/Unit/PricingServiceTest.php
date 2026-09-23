<?php

namespace Tests\Unit;

use App\Models\PricingConfiguration;
use App\Services\PricingService;
use Exception;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    protected PricingConfiguration $pricing;

    protected function setUp(): void
    {
        parent::setUp();

        // Nonaktifkan konfigurasi lama agar terisolasi
        PricingConfiguration::query()->update(['is_active' => false]);

        $this->pricing = PricingConfiguration::create([
            'name' => 'Tarif Uji Unit Test',
            'base_price' => 100000,
            'price_per_kilometer' => 5000,
            'extra_passenger_price' => 25000,
            'parking_fee' => 10000,
            'toll_fee' => 15000,
            'night_service_fee' => 0,
            'minimum_price' => 200000,
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        $this->pricing->delete();
        PricingConfiguration::where('id', 1)->update(['is_active' => true]);
        parent::tearDown();
    }

    /**
     * Memverifikasi formula perhitungan biaya dasar, jarak, dan penumpang.
     */
    public function test_pricing_calculation_formula(): void
    {
        $service = new PricingService();

        // Jarak 20 km, 3 penumpang (1 base, 2 ekstra = 50.000)
        // base: 100.000
        // distance: 20 * 5.000 = 100.000
        // passenger: 2 * 25.000 = 50.000
        // additional: 10.000 (parkir) + 15.000 (tol) = 25.000
        // subtotal: 275.000 (> minimum 200.000)
        // total: 275.000
        $result = $service->calculate(20.0, 3);

        $this->assertEquals('IDR', $result['currency']);
        $this->assertEquals(100000, $result['base_cost']);
        $this->assertEquals(100000, $result['distance_cost']);
        $this->assertEquals(50000, $result['passenger_cost']);
        $this->assertEquals(10000, $result['parking_cost']);
        $this->assertEquals(15000, $result['toll_cost']);
        $this->assertEquals(275000, $result['subtotal']);
        $this->assertFalse($result['minimum_price_applied']);
        $this->assertEquals(275000, $result['estimated_total']);
        $this->assertEquals('Rp275.000', $result['formatted_total']);
    }

    /**
     * Memverifikasi minimum price diterapkan ketika subtotal lebih rendah.
     */
    public function test_minimum_price_applied_when_subtotal_is_lower(): void
    {
        $service = new PricingService();

        // Jarak 2 km, 1 penumpang
        // base: 100.000
        // distance: 2 * 5.000 = 10.000
        // additional: 25.000
        // subtotal = 135.000 < minimum 200.000
        $result = $service->calculate(2.0, 1);

        $this->assertTrue($result['minimum_price_applied']);
        $this->assertEquals(200000, $result['estimated_total']);
        $this->assertEquals('Rp200.000', $result['formatted_total']);
    }

    /**
     * Memverifikasi pembulatan harga selalu ke ribuan terdekat ke atas (ceil).
     */
    public function test_rounding_ceil_to_nearest_thousand(): void
    {
        $service = new PricingService();

        // Jarak 21.125 km, 1 penumpang
        // base: 100.000
        // distance: 21.125 * 5.000 = 105.625
        // additional: 25.000
        // subtotal = 230.625
        // ceil(230.625 / 1000) * 1000 = 231.000
        $result = $service->calculate(21.125, 1);

        $this->assertEquals(231000, $result['estimated_total']);
        $this->assertEquals('Rp231.000', $result['formatted_total']);
    }

    /**
     * Memverifikasi penanganan jarak nol.
     */
    public function test_zero_distance_handling(): void
    {
        $service = new PricingService();

        // Jarak 0 km -> distance_cost = 0
        $result = $service->calculate(0.0, 1);

        $this->assertEquals(0, $result['distance_cost']);
        $this->assertEquals(200000, $result['estimated_total']); // Minimum price
    }

    /**
     * Memverifikasi exception dilempar bila tidak ada konfigurasi tarif aktif.
     */
    public function test_exception_thrown_when_no_active_pricing_configuration(): void
    {
        PricingConfiguration::query()->update(['is_active' => false]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Estimasi biaya belum dapat dihitung karena tarif belum dikonfigurasi.');

        $service = new PricingService();
        $service->calculate(15.0, 1);
    }
}
