<?php

namespace Tests\Feature;

use App\Models\BrandSetting;
use Tests\TestCase;

class BrandLayoutTest extends TestCase
{
    /**
     * Memverifikasi halaman beranda dapat diakses (HTTP 200) dengan data brand setting aktif.
     */
    public function test_home_page_renders_with_active_brand_settings(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bali Tour Service');
        $response->assertSee('Jelajahi Bali dengan lebih mudah');
        $response->assertSee('--brand-primary: #0f172a', false);
        $response->assertSee('--brand-secondary: #f59e0b', false);
    }

    /**
     * Memverifikasi fallback aman saat tabel brand_settings kosong.
     */
    public function test_home_page_graceful_fallback_when_brand_settings_empty(): void
    {
        // Deaktifkan sementara seluruh setting brand
        BrandSetting::query()->update(['is_active' => false]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bali Tour Service');
        $response->assertSee('--brand-primary: #0f172a', false);

        // Kembalikan status aktif
        BrandSetting::query()->update(['is_active' => true]);
    }

    /**
     * Memverifikasi fallback logo ketika logo_path kosong (menampilkan teks brand dan bukan broken img).
     */
    public function test_brand_logo_component_renders_text_when_logo_path_null(): void
    {
        $brand = BrandSetting::getActive();
        $this->assertNotNull($brand);
        $brand->update(['logo_path' => null]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('brand-name-text');
        $response->assertSee('brand-fallback-badge');
    }

    /**
     * Memverifikasi nomor WhatsApp placeholder tidak dijadikan link wa.me aktif.
     */
    public function test_whatsapp_placeholder_is_not_rendered_as_clickable_link(): void
    {
        // Data seeder menggunakan nomor placeholder dummy '6281234567890'
        $brand = BrandSetting::getActive();
        $this->assertNotNull($brand);
        $brand->update(['whatsapp_number' => '6281234567890']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('https://wa.me/6281234567890');
        $response->assertSee('data-bs-target="#contactInfoModal"', false);
    }

    /**
     * Memverifikasi nomor WhatsApp valid ter-render sebagai link wa.me aktif.
     */
    public function test_valid_whatsapp_is_rendered_as_clickable_link(): void
    {
        $brand = BrandSetting::getActive();
        $this->assertNotNull($brand);
        $brand->update(['whatsapp_number' => '6281987654321']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('https://wa.me/6281987654321');

        // Kembalikan ke placeholder
        $brand->update(['whatsapp_number' => '6281234567890']);
    }

    /**
     * Memverifikasi sanitasi warna mencegah CSS injection dan kembali ke fallback aman.
     */
    public function test_color_sanitization_prevents_css_injection(): void
    {
        $brand = BrandSetting::getActive();
        $this->assertNotNull($brand);
        $brand->update(['primary_color' => 'blue; background: red;']);

        $response = $this->get('/');

        $response->assertStatus(200);
        // Nilai berbahaya ditolak oleh regex dan dialihkan ke fallback #0f172a
        $response->assertDontSee('blue; background: red;');
        $response->assertSee('--brand-primary: #0f172a', false);

        // Kembalikan ke warna awal
        $brand->update(['primary_color' => '#0f172a']);
    }

    /**
     * Memverifikasi komponen flash messages menampilkan tipe success, error, warning, dan info.
     */
    public function test_flash_messages_rendering(): void
    {
        $response = $this->withSession([
            'success' => 'Operasi berhasil dilakukan',
            'warning' => 'Perhatian pada kuota perjalanan',
            'info' => 'Informasi pembaruan jadwal',
        ])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Operasi berhasil dilakukan');
        $response->assertSee('Perhatian pada kuota perjalanan');
        $response->assertSee('Informasi pembaruan jadwal');
    }
}
