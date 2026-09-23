<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan form login admin.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdminActive()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Memproses percobaan login admin dengan rate limiting dan regenerasi session.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Rate limiting: batasi 5 kali percobaan per IP/Email dalam 1 menit
        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$seconds} detik.",
                ]);
        }

        $remember = $request->boolean('remember');

        // Otentikasi dengan verifikasi aktif
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => true], $remember)) {
            $user = Auth::user();

            // Verifikasi status administrator
            if (!$user->is_admin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                RateLimiter::hit($throttleKey);

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'Akun Anda tidak memiliki hak akses administrator.',
                    ]);
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->name . '.');
        }

        RateLimiter::hit($throttleKey);

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
            ]);
    }

    /**
     * Memproses keluar (logout) admin secara aman dengan method POST.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('info', 'Anda telah berhasil keluar dari sesi administrator.');
    }
}
