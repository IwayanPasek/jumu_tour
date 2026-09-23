<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use App\Models\PricingConfiguration;
use App\Models\Region;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama admin dengan ringkasan status master data.
     */
    public function index(): View
    {
        $stats = [
            'total_regions' => Region::count(),
            'total_categories' => Category::count(),
            'total_destinations' => Destination::count(),
            'active_pricing' => PricingConfiguration::getActive(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
