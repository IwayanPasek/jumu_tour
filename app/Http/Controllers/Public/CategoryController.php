<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori wisata aktif beserta jumlah destinasi aktif.
     */
    public function index(): View
    {
        $categories = Category::active()
            ->withCount(['destinations' => function ($query) {
                $query->active();
            }])
            ->orderBy('name', 'asc')
            ->paginate(12);

        return view('public.categories.index', compact('categories'));
    }

    /**
     * Menampilkan detail kategori wisata dan destinasi aktif yang termasuk di dalamnya.
     */
    public function show(string $slug): View
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $destinations = $category->destinations()
            ->active()
            ->with('region')
            ->ordered()
            ->paginate(9);

        return view('public.categories.show', compact('category', 'destinations'));
    }
}
