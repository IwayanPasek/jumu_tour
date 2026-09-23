<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\Admin\RegionController as AdminRegionController;
use App\Http\Controllers\Public\CalculatorController as PublicCalculatorController;
use App\Http\Controllers\Public\CategoryController as PublicCategoryController;
use App\Http\Controllers\Public\DestinationController as PublicDestinationController;
use App\Http\Controllers\Public\HomeController as PublicHomeController;
use App\Http\Controllers\Public\RegionController as PublicRegionController;
use App\Http\Controllers\Public\RouteController as PublicRouteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes - Website Tour Bali
|--------------------------------------------------------------------------
*/

// Language Switcher
Route::get('/language/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// Beranda
Route::get('/', [PublicHomeController::class, 'index'])->name('home');

// Daerah Wisata
Route::get('/regions', [PublicRegionController::class, 'index'])->name('regions.index');
Route::get('/regions/{slug}', [PublicRegionController::class, 'show'])->name('regions.show');

// Kategori Wisata
Route::get('/categories', [PublicCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [PublicCategoryController::class, 'show'])->name('categories.show');

// Tempat Wisata (Destinations)
Route::get('/destinations', [PublicDestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{slug}', [PublicDestinationController::class, 'show'])->name('destinations.show');

// Kalkulator Rute & Estimasi Biaya
Route::get('/calculator', [PublicCalculatorController::class, 'index'])->name('calculator.index');
Route::post('/calculator/route', [PublicRouteController::class, 'calculate'])
    ->middleware('throttle:20,1')
    ->name('calculator.route');
Route::post('/calculator/estimate', [PublicRouteController::class, 'calculate'])
    ->middleware('throttle:10,1')
    ->name('calculator.estimate');

// Pemesanan Tour via WhatsApp Click-to-Chat
Route::post('/booking/whatsapp', [\App\Http\Controllers\Public\BookingController::class, 'generateWhatsAppLink'])
    ->middleware('throttle:10,1')
    ->name('booking.whatsapp');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    // Guest Admin Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('admin.login.store');
    });

    // Authenticated Admin Routes (Dilindungi Middleware admin)
    Route::middleware('admin')->name('admin.')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Kelola Daerah (Regions)
        Route::patch('regions/{region}/status', [AdminRegionController::class, 'toggleStatus'])->name('regions.status');
        Route::resource('regions', AdminRegionController::class)->except(['show']);

        // Kelola Kategori (Categories)
        Route::patch('categories/{category}/status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.status');
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        // Kelola Destinasi Wisata (Destinations)
        Route::patch('destinations/{destination}/status', [AdminDestinationController::class, 'toggleStatus'])->name('destinations.status');
        Route::delete('destinations/{destination}/image', [AdminDestinationController::class, 'destroyImage'])->name('destinations.image.destroy');
        Route::resource('destinations', AdminDestinationController::class);
    });
});
