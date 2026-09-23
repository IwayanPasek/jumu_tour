<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRegionRequest;
use App\Http\Requests\Admin\UpdateRegionRequest;
use App\Models\Region;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegionController extends Controller
{
    /**
     * Menampilkan daftar daerah dengan pencarian dan pagination.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $regions = Region::query()
            ->withCount('destinations')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('regency', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.regions.index', compact('regions', 'search'));
    }

    /**
     * Menampilkan form pembuatan daerah baru.
     */
    public function create(): View
    {
        $region = new Region([
            'is_active' => true,
        ]);

        return view('admin.regions.create', compact('region'));
    }

    /**
     * Menyimpan data daerah baru ke database.
     */
    public function store(StoreRegionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = SlugService::createUniqueSlug(
            Region::class,
            $validated['name'],
            $validated['slug'] ?? null
        );

        $region = Region::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'regency' => $validated['regency'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.regions.index')
            ->with('success', "Daerah '{$region->name}' berhasil ditambahkan.");
    }

    /**
     * Menampilkan form edit daerah.
     */
    public function edit(Region $region): View
    {
        return view('admin.regions.edit', compact('region'));
    }

    /**
     * Memperbarui data daerah di database.
     */
    public function update(UpdateRegionRequest $request, Region $region): RedirectResponse
    {
        $validated = $request->validated();

        $slug = SlugService::createUniqueSlug(
            Region::class,
            $validated['name'],
            $validated['slug'] ?? null,
            $region->id
        );

        $region->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'regency' => $validated['regency'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.regions.index')
            ->with('success', "Data daerah '{$region->name}' berhasil diperbarui.");
    }

    /**
     * Menghapus daerah jika tidak memiliki destinasi terkait.
     */
    public function destroy(Region $region): RedirectResponse
    {
        if ($region->destinations()->exists()) {
            return back()->with(
                'error',
                'Daerah tidak dapat dihapus karena masih memiliki destinasi wisata terkait. Silakan nonaktifkan daerah atau pindahkan destinasi terlebih dahulu.'
            );
        }

        $name = $region->name;
        $region->delete();

        return redirect()
            ->route('admin.regions.index')
            ->with('success', "Daerah '{$name}' berhasil dihapus.");
    }

    /**
     * Mengubah status aktif / nonaktif daerah secara cepat.
     */
    public function toggleStatus(Region $region): RedirectResponse
    {
        $newStatus = !$region->is_active;
        $region->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status daerah '{$region->name}' berhasil {$statusText}.");
    }
}
