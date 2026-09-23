<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDestinationRequest;
use App\Http\Requests\Admin\UpdateDestinationRequest;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DestinationController extends Controller
{
    /**
     * Menampilkan daftar destinasi wisata dengan filter, pencarian, dan pagination.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $regionId = $request->query('region_id');
        $categoryId = $request->query('category_id');
        $status = $request->query('status');

        $query = Destination::query()
            ->with(['region', 'category']);

        // Filter Pencarian Teks (Nama atau Alamat)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter Daerah (Region)
        if (!empty($regionId)) {
            $query->where('region_id', $regionId);
        }

        // Filter Kategori (Category)
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // Filter Status Aktif
        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        $destinations = $query
            ->orderBy('display_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        $regions = Region::orderBy('name')->get(['id', 'name']);
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.destinations.index', compact(
            'destinations',
            'regions',
            'categories',
            'search',
            'regionId',
            'categoryId',
            'status'
        ));
    }

    /**
     * Menampilkan form pembuatan destinasi baru.
     */
    public function create(): View
    {
        $destination = new Destination([
            'is_active' => true,
            'display_order' => 0,
        ]);

        $regions = Region::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.destinations.create', compact('destination', 'regions', 'categories'));
    }

    /**
     * Menyimpan destinasi wisata baru ke database beserta file gambar utama.
     */
    public function store(StoreDestinationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = SlugService::createUniqueSlug(
            Destination::class,
            $validated['name'],
            $validated['slug'] ?? null
        );

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('destinations', 'public');
        }

        $destination = Destination::create([
            'region_id' => $validated['region_id'],
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'image_path' => $imagePath,
            'is_active' => $request->boolean('is_active', true),
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.destinations.index')
            ->with('success', "Tempat wisata '{$destination->name}' berhasil ditambahkan.");
    }

    /**
     * Menampilkan detail informasi destinasi wisata di panel admin.
     */
    public function show(Destination $destination): View
    {
        $destination->load(['region', 'category']);

        return view('admin.destinations.show', compact('destination'));
    }

    /**
     * Menampilkan form edit destinasi wisata.
     */
    public function edit(Destination $destination): View
    {
        $regions = Region::orderBy('name')->get(['id', 'name', 'is_active']);
        $categories = Category::orderBy('name')->get(['id', 'name', 'is_active']);

        return view('admin.destinations.edit', compact('destination', 'regions', 'categories'));
    }

    /**
     * Memperbarui data destinasi wisata dan mengganti gambar jika diunggah baru.
     */
    public function update(UpdateDestinationRequest $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validated();

        $slug = SlugService::createUniqueSlug(
            Destination::class,
            $validated['name'],
            $validated['slug'] ?? null,
            $destination->id
        );

        $imagePath = $destination->image_path;

        if ($request->hasFile('image')) {
            // Simpan gambar baru ke storage
            $newImagePath = $request->file('image')->store('destinations', 'public');

            // Hapus gambar lama jika ada
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $newImagePath;
        }

        $destination->update([
            'region_id' => $validated['region_id'],
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'image_path' => $imagePath,
            'is_active' => $request->boolean('is_active'),
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.destinations.index')
            ->with('success', "Data tempat wisata '{$destination->name}' berhasil diperbarui.");
    }

    /**
     * Menghapus destinasi wisata beserta file gambar dari storage.
     */
    public function destroy(Destination $destination): RedirectResponse
    {
        $name = $destination->name;

        // Hapus file gambar jika ada
        if ($destination->image_path && Storage::disk('public')->exists($destination->image_path)) {
            Storage::disk('public')->delete($destination->image_path);
        }

        $destination->delete();

        return redirect()
            ->route('admin.destinations.index')
            ->with('success', "Tempat wisata '{$name}' berhasil dihapus.");
    }

    /**
     * Mengubah status aktif / nonaktif destinasi wisata secara cepat.
     */
    public function toggleStatus(Destination $destination): RedirectResponse
    {
        $newStatus = !$destination->is_active;
        $destination->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status destinasi '{$destination->name}' berhasil {$statusText}.");
    }

    /**
     * Menghapus file gambar utama destinasi tanpa menghapus data record destinasi.
     */
    public function destroyImage(Destination $destination): RedirectResponse
    {
        if ($destination->image_path) {
            if (Storage::disk('public')->exists($destination->image_path)) {
                Storage::disk('public')->delete($destination->image_path);
            }

            $destination->update(['image_path' => null]);
        }

        return back()->with('success', "Foto utama destinasi '{$destination->name}' berhasil dihapus.");
    }
}
