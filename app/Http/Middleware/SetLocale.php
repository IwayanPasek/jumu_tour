<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales.
     *
     * @var array<string>
     */
    protected array $supportedLocales = ['en', 'id'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Periksa parameter query 'lang' jika diberikan
        if ($request->has('lang') && in_array($request->query('lang'), $this->supportedLocales, true)) {
            $locale = $request->query('lang');
            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
        }

        // 2. Periksa session jika belum ditentukan
        if (! $locale && $request->hasSession() && $request->session()->has('locale')) {
            $sessionLocale = $request->session()->get('locale');
            if (in_array($sessionLocale, $this->supportedLocales, true)) {
                $locale = $sessionLocale;
            }
        }

        // 3. Periksa cookie 'locale' jika belum ditentukan
        if (! $locale && $request->hasCookie('locale')) {
            $cookieLocale = $request->cookie('locale');
            if (in_array($cookieLocale, $this->supportedLocales, true)) {
                $locale = $cookieLocale;
            }
        }

        // 4. Fallback ke konfigurasi default (English 'en')
        if (! $locale || ! in_array($locale, $this->supportedLocales, true)) {
            $locale = config('app.locale', 'en');
        }

        // Terapkan locale ke Laravel Application dan Carbon Date Formatter
        App::setLocale($locale);
        Carbon::setLocale($locale);

        $response = $next($request);

        // Ambil locale akhir setelah penanganan request/controller
        $finalLocale = App::getLocale();
        if ($request->hasSession() && $request->session()->has('locale')) {
            $sessionLocale = $request->session()->get('locale');
            if (in_array($sessionLocale, $this->supportedLocales, true)) {
                $finalLocale = $sessionLocale;
            }
        }

        // Pasang cookie locale jika belum ada atau berbeda
        if (! $request->hasCookie('locale') || $request->cookie('locale') !== $finalLocale) {
            $response->headers->setCookie(cookie('locale', $finalLocale, 60 * 24 * 365, null, null, false, false));
        }

        return $response;
    }
}
