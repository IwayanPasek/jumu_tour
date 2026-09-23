<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    /**
     * Memverifikasi default locale aplikasi adalah 'en' (English-first).
     */
    public function test_default_locale_is_en(): void
    {
        $this->assertEquals('en', Config::get('app.locale'));
        $this->assertEquals('en', Config::get('app.fallback_locale'));
    }

    /**
     * Memverifikasi route language switch dapat mengubah locale ke Bahasa Indonesia ('id').
     */
    public function test_can_switch_locale_to_indonesian(): void
    {
        $response = $this->get(route('language.switch', ['locale' => 'id']));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'id');
        $response->assertCookie('locale', 'id');
    }

    /**
     * Memverifikasi route language switch dapat mengubah locale ke English ('en').
     */
    public function test_can_switch_locale_to_english(): void
    {
        $response = $this->withSession(['locale' => 'id'])
            ->get(route('language.switch', ['locale' => 'en']));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
        $response->assertCookie('locale', 'en');
    }

    /**
     * Memverifikasi locale yang tidak didukung akan fallback ke default 'en'.
     */
    public function test_unsupported_locale_falls_back_to_en(): void
    {
        $response = $this->get(route('language.switch', ['locale' => 'fr']));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
    }

    /**
     * Memverifikasi proteksi open-redirect pada language switcher.
     */
    public function test_prevents_open_redirect_on_switch(): void
    {
        $response = $this->from('https://evil-site.com/phishing')
            ->get(route('language.switch', ['locale' => 'id']));

        // Harus dialihkan ke home, bukan ke evil-site.com
        $response->assertRedirect(route('home'));
    }

    /**
     * Memverifikasi halaman publik beranda menampilkan konten English secara default.
     */
    public function test_public_homepage_renders_in_english_by_default(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('html lang="en"', false);
        $response->assertSee(__('home.core_values'));
        $response->assertSee(__('hero.explore_button'));
    }

    /**
     * Memverifikasi halaman publik beranda menampilkan konten Bahasa Indonesia saat session 'id' aktif.
     */
    public function test_public_homepage_renders_in_indonesian_when_locale_is_id(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get(route('home'));

        $response->assertOk();
        $response->assertSee('html lang="id"', false);
        $response->assertSee('Nilai Utama');
        $response->assertSee('Jelajahi Tempat Wisata Populer');
    }

    /**
     * Memverifikasi halaman kalkulator merender label sesuai locale aktif.
     */
    public function test_calculator_renders_bilingual_labels(): void
    {
        // Test English
        $responseEn = $this->withSession(['locale' => 'en'])->get(route('calculator.index'));
        $responseEn->assertOk();
        $responseEn->assertSee('Bali Custom Tour Route Planner');
        $responseEn->assertSee('1. Pickup Location');

        // Test Indonesian
        $responseId = $this->withSession(['locale' => 'id'])->get(route('calculator.index'));
        $responseId->assertOk();
        $responseId->assertSee('Rencana Rute Perjalanan Bali');
        $responseId->assertSee('1. Titik Jemput (Pickup)');
    }

    /**
     * Memverifikasi WhatsAppService menghasilkan pesan bilingual (EN & ID).
     */
    public function test_whatsapp_service_formats_messages_bilingually(): void
    {
        $whatsAppService = app(WhatsAppService::class);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_phone' => '08123456789',
            'tour_date' => '2026-10-15',
            'passenger_count' => 2,
            'pickup_name' => 'Ngurah Rai Airport (DPS)',
            'destinations' => [
                ['id' => 1, 'name' => 'Uluwatu Temple'],
                ['id' => 2, 'name' => 'Tanah Lot'],
            ],
            'distance_km' => '65.5',
            'duration_text' => '2 hours 15 mins',
            'formatted_price' => 'Rp750.000',
            'notes' => 'Need English-speaking driver',
        ];

        // Format English
        $msgEn = $whatsAppService->formatTourBookingMessage($bookingData, 'en');
        $this->assertStringContainsString('Customer Details: John Doe', $msgEn);
        $this->assertStringContainsString('Travel Date: 2026-10-15', $msgEn);
        $this->assertStringContainsString('Total Distance: 65.5 km', $msgEn);
        $this->assertStringContainsString('Estimated Price: Rp750.000', $msgEn);

        // Format Indonesian
        $msgId = $whatsAppService->formatTourBookingMessage($bookingData, 'id');
        $this->assertStringContainsString('Data Pelanggan: John Doe', $msgId);
        $this->assertStringContainsString('Tanggal Perjalanan: 2026-10-15', $msgId);
        $this->assertStringContainsString('Total Jarak: 65.5 km', $msgId);
        $this->assertStringContainsString('Estimasi Biaya: Rp750.000', $msgId);
    }

    /**
     * Memverifikasi komponen language switcher muncul di beranda dan halaman login.
     */
    public function test_language_switcher_is_rendered_in_views(): void
    {
        // Navbar publik
        $publicRes = $this->get(route('home'));
        $publicRes->assertOk();
        $publicRes->assertSee('dropdown-toggle');
        $publicRes->assertSee('English');
        $publicRes->assertSee('Bahasa Indonesia');

        // Login page
        $loginRes = $this->get(route('admin.login'));
        $loginRes->assertOk();
        $loginRes->assertSee('English');
        $loginRes->assertSee('Bahasa Indonesia');
    }
}
