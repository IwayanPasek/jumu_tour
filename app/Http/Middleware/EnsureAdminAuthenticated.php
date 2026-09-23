<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    /**
     * Memastikan request berasal dari user admin yang terautentikasi dan aktif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->guest(route('admin.login'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses dashboard admin.');
        }

        $user = Auth::user();

        if (!$user->is_admin || !$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'Akses ditolak. Akun Anda tidak memiliki hak akses administrator.');
        }

        return $next($request);
    }
}
