<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori dengan pencarian dan pagination.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $categories = Category::query()
            ->withCount('destinations')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    /**
     * Menampilkan form pembuatan kategori baru.
     */
    public function create(): View
    {
        $category = new Category([
            'is_active' => true,
        ]);

        return view('admin.categories.create', compact('category'));
    }

    /**
     * Menyimpan data kategori baru ke database.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = SlugService::createUniqueSlug(
            Category::class,
            $validated['name'],
            $validated['slug'] ?? null
        );

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
    }

    /**
     * Menampilkan form edit kategori.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Memperbarui data kategori di database.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $slug = SlugService::createUniqueSlug(
            Category::class,
            $validated['name'],
            $validated['slug'] ?? null,
            $category->id
        );

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Data kategori '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Menghapus kategori jika tidak memiliki destinasi terkait.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->destinations()->exists()) {
            return back()->with(
                'error',
                'Kategori tidak dapat dihapus karena masih digunakan oleh destinasi wisata terkait. Silakan nonaktifkan kategori terlebih dahulu.'
            );
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }

    /**
     * Mengubah status aktif / nonaktif kategori secara cepat.
     */
    public function toggleStatus(Category $category): RedirectResponse
    {
        $newStatus = !$category->is_active;
        $category->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status kategori '{$category->name}' berhasil {$statusText}.");
    }
}
