<?php

namespace Tests\Unit;

use App\Services\WhatsAppService;
use Exception;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    /**
     * Memverifikasi normalisasi nomor lokal 08xx menjadi 628xx.
     */
    public function test_normalizes_indonesian_local_phone_number(): void
    {
        $service = new WhatsAppService();

        $this->assertEquals('628123456789', $service->normalizePhoneNumber('08123456789'));
        $this->assertEquals('6281234567890', $service->normalizePhoneNumber('0812-3456-7890'));
        $this->assertEquals('6281234567890', $service->normalizePhoneNumber('(0812) 3456 7890'));
    }

    /**
     * Memverifikasi normalisasi nomor berawalan tanda plus (+).
     */
    public function test_normalizes_international_plus_sign(): void
    {
        $service = new WhatsAppService();

        $this->assertEquals('6281234567890', $service->normalizePhoneNumber('+62 812-3456-7890'));
    }

    /**
     * Memverifikasi nomor kosong melempar exception.
     */
    public function test_empty_phone_number_throws_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Nomor telepon tidak boleh kosong.');

        $service = new WhatsAppService();
        $service->normalizePhoneNumber('');
    }

    /**
     * Memverifikasi nomor terlalu pendek melempar exception.
     */
    public function test_too_short_phone_number_throws_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Panjang nomor telepon tidak valid');

        $service = new WhatsAppService();
        $service->normalizePhoneNumber('12345');
    }

    /**
     * Memverifikasi URL wa.me dihasilkan dengan query parameter encoded yang tepat.
     */
    public function test_generates_valid_wa_me_url(): void
    {
        $service = new WhatsAppService();

        $url = $service->generateClickToChatUrl('08123456789', 'Halo, saya ingin tour & jalan-jalan!');

        $this->assertStringStartsWith('https://wa.me/628123456789?text=', $url);
        $this->assertStringContainsString('Halo%2C%20saya%20ingin%20tour%20%26%20jalan-jalan%21', $url);
    }

    /**
     * Memverifikasi template pesan booking tidak membocorkan informasi rahasia.
     */
    public function test_booking_message_does_not_contain_secrets(): void
    {
        $service = new WhatsAppService();

        $message = $service->formatTourBookingMessage([
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '08123456789',
            'tour_date' => '25-12-2026',
            'passenger_count' => 4,
            'pickup_name' => 'Bandara DPS',
            'destinations' => ['Pantai Kuta', 'Tanah Lot'],
            'distance_km' => '45.5',
            'duration_text' => '2 jam',
            'formatted_price' => 'Rp550.000',
        ]);

        $this->assertStringContainsString('Budi Santoso', $message);
        $this->assertStringContainsString('Bandara DPS', $message);
        $this->assertStringContainsString('Pantai Kuta', $message);
        $this->assertStringContainsString('Rp550.000', $message);

        // Pastikan tidak ada API key atau password di pesan
        $this->assertStringNotContainsString('API_KEY', $message);
        $this->assertStringNotContainsString('password', $message);
        $this->assertStringNotContainsString('secret', $message);
    }
}
