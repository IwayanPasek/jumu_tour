<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Supported locales list.
     *
     * @var array<string>
     */
    protected array $supportedLocales = ['en', 'id'];

    /**
     * Switch application language and redirect safely.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        // 1. Validasi locale whitelist ketat
        if (! in_array($locale, $this->supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        // 2. Simpan pilihan bahasa ke sesi dan aplikasikan ke aplikasi
        $request->session()->put('locale', $locale);
        \Illuminate\Support\Facades\App::setLocale($locale);

        // 3. Tentukan URL tujuan secara aman (Pencegahan Open Redirect)
        $previousUrl = url()->previous();
        $appHost = $request->getHost();
        $targetHost = parse_url($previousUrl, PHP_URL_HOST);

        // Jika URL sebelumnya internal dan host-nya cocok, kembali ke halaman tersebut
        if (! empty($previousUrl) && ($targetHost === null || $targetHost === $appHost)) {
            return redirect()->to($previousUrl)->withCookie(cookie('locale', $locale, 60 * 24 * 365, null, null, false, false));
        }

        // Fallback aman ke beranda
        return redirect()->route('home')->withCookie(cookie('locale', $locale, 60 * 24 * 365, null, null, false, false));
    }
}
