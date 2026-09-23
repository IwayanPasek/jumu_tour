<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda publik.
     */
    public function index(): View
    {
        return view('public.home');
    }
}
