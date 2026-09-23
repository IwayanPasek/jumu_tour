<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    /**
     * Menampilkan katalog destinasi wisata publik dengan filter daerah, kategori, dan pencarian.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $regionSlug = $request->query('region');
        $categorySlug = $request->query('category');

        $query = Destination::query()
            ->where('is_active', true)
            ->whereHas('region', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['region', 'category']);

        // Filter Pencarian Teks
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter Berdasarkan Slug Daerah
        if (!empty($regionSlug)) {
            $query->whereHas('region', function ($q) use ($regionSlug) {
                $q->where('slug', $regionSlug);
            });
        }

        // Filter Berdasarkan Slug Kategori
        if (!empty($categorySlug)) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $destinations = $query
            ->orderBy('display_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(9)
            ->withQueryString();

        $regions = Region::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('public.destinations.index', compact(
            'destinations',
            'regions',
            'categories',
            'search',
            'regionSlug',
            'categorySlug'
        ));
    }

    /**
     * Menampilkan halaman detail destinasi wisata publik.
     */
    public function show(string $slug): View
    {
        $destination = Destination::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('region', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['region', 'category'])
            ->firstOrFail();

        // Destinasi lain di daerah yang sama (rekomendasi wisata sekitar)
        $nearbyDestinations = Destination::query()
            ->where('region_id', $destination->region_id)
            ->where('id', '!=', $destination->id)
            ->where('is_active', true)
            ->with(['region', 'category'])
            ->orderBy('display_order', 'asc')
            ->take(3)
            ->get();

        return view('public.destinations.show', compact('destination', 'nearbyDestinations'));
    }
}
