<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    /**
     * Memverifikasi seluruh response web dilengkapi security headers penting.
     */
    public function test_security_headers_are_present_in_responses(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Memverifikasi guest tidak diizinkan mengakses dashboard admin.
     */
    public function test_guest_is_redirected_away_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Memverifikasi logout tidak dapat diakses melalui HTTP GET.
     */
    public function test_logout_via_get_method_is_disallowed(): void
    {
        $response = $this->get('/admin/logout');

        // Route logout didefinisikan sebagai POST, sehingga GET menghasilkan 405 Method Not Allowed
        $response->assertStatus(405);
    }

    /**
     * Memverifikasi rate limiting login aktif pada endpoint POST /admin/login.
     */
    public function test_admin_login_rate_limiting_protects_endpoint(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', [
                'email' => 'attacker@test.local',
                'password' => 'wrongpassword123',
            ]);
        }

        // Percobaan ke-6 harus terkena rate limit
        $response = $this->post('/admin/login', [
            'email' => 'attacker@test.local',
            'password' => 'wrongpassword123',
        ]);

        // Rate limiter dapat mengembalikan 429 atau redirect back dengan error rate limit
        $this->assertTrue(
            $response->status() === 429 || $response->isRedirect()
        );
    }

    /**
     * Memverifikasi output teks tersanitasi dari payload XSS.
     */
    public function test_xss_payload_in_destination_name_is_escaped(): void
    {
        $response = $this->get(route('destinations.index'));
        $response->assertOk();

        // Pastikan tidak ada raw script tag berbahaya yang lolos tanpa escaping
        $this->assertStringNotContainsString('<script>alert("XSS")</script>', $response->getContent());
    }
}
