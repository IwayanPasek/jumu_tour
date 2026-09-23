<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    /**
     * Memverifikasi halaman login admin dapat diakses oleh tamu (guest).
     */
    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('Portal Administrator');
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
    }

    /**
     * Memverifikasi admin dapat masuk (login) dengan kredensial yang valid.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@balitourservice.local'))->first();
        $this->assertNotNull($admin);

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'AdminJumu2026!',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Memverifikasi login gagal jika kata sandi salah.
     */
    public function test_admin_cannot_login_with_invalid_password(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@balitourservice.local'))->first();
        $this->assertNotNull($admin);

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'PasswordSalah123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Memverifikasi user non-admin tidak dapat masuk ke dashboard admin.
     */
    public function test_non_admin_user_cannot_login_to_admin_portal(): void
    {
        $nonAdmin = User::create([
            'name' => 'User Biasa',
            'email' => 'user.biasa@example.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => $nonAdmin->email,
            'password' => 'Password123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');

        $nonAdmin->delete();
    }

    /**
     * Memverifikasi akun admin yang dinonaktifkan (is_active = false) ditolak masuk.
     */
    public function test_inactive_admin_cannot_login(): void
    {
        $inactiveAdmin = User::create([
            'name' => 'Admin Nonaktif',
            'email' => 'admin.nonaktif@example.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => true,
            'is_active' => false,
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => $inactiveAdmin->email,
            'password' => 'Password123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');

        $inactiveAdmin->delete();
    }

    /**
     * Memverifikasi dashboard admin tidak dapat diakses tanpa login (redirect ke login).
     */
    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Memverifikasi user non-admin mendapat 403 saat mengakses dashboard secara langsung.
     */
    public function test_non_admin_gets_403_on_dashboard(): void
    {
        $nonAdmin = User::create([
            'name' => 'Pengguna Biasa',
            'email' => 'biasa@example.com',
            'password' => Hash::make('Password123!'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($nonAdmin)->get(route('admin.dashboard'));

        $response->assertStatus(403);

        $nonAdmin->delete();
    }

    /**
     * Memverifikasi admin yang login dapat mengakses dashboard dengan sukses.
     */
    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@balitourservice.local'))->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang, ' . $admin->name);
        $response->assertSee('Daerah Wisata');
        $response->assertSee('Kategori Wisata');
        $response->assertSee('Tempat Wisata');
    }

    /**
     * Memverifikasi admin dapat keluar (logout) secara aman melalui POST.
     */
    public function test_admin_can_logout(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@balitourservice.local'))->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->post(route('admin.logout'));

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Memverifikasi admin yang sudah login dialihkan dari halaman login ke dashboard.
     */
    public function test_authenticated_admin_is_redirected_from_login_page(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@balitourservice.local'))->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('admin.login'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Memverifikasi registrasi publik tidak tersedia (404 Not Found).
     */
    public function test_public_registration_is_disabled(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(404);

        $postResponse = $this->post('/register', [
            'name' => 'Attacker',
            'email' => 'attacker@example.com',
            'password' => 'secret123',
        ]);
        $postResponse->assertStatus(404);
    }
}
