<?php

namespace App\Providers;

use App\Http\View\Composers\BrandViewComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan view pagination Bootstrap 5 resmi
        Paginator::useBootstrapFive();

        // Bagikan data brand aktif ke seluruh layout, partial, dan view publik
        View::composer(['layouts.*', 'partials.*', 'components.*', 'public.*'], BrandViewComposer::class);
    }
}
