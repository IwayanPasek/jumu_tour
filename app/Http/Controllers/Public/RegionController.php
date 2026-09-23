<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\View\View;

class RegionController extends Controller
{
    /**
     * Menampilkan daftar daerah/kabupaten aktif di Bali beserta jumlah destinasi aktif.
     */
    public function index(): View
    {
        $regions = Region::active()
            ->withCount(['destinations' => function ($query) {
                $query->active();
            }])
            ->orderBy('name', 'asc')
            ->paginate(12);

        return view('public.regions.index', compact('regions'));
    }

    /**
     * Menampilkan detail satu daerah dan daftar destinasi aktif di dalamnya.
     */
    public function show(string $slug): View
    {
        $region = Region::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $destinations = $region->destinations()
            ->active()
            ->with('category')
            ->ordered()
            ->paginate(9);

        return view('public.regions.show', compact('region', 'destinations'));
    }
}
