<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use Tests\TestCase;

class RegionCategoryTest extends TestCase
{
    /**
     * Memverifikasi halaman daftar daerah (regions.index) menampilkan daerah aktif dan jumlah destinasi.
     */
    public function test_regions_index_renders_active_regions_with_destination_count(): void
    {
        $response = $this->get(route('regions.index'));

        $response->assertStatus(200);
        $response->assertSee('Badung');
        $response->assertSee('Gianyar');
        $response->assertSee('Destinasi');
    }

    /**
     * Memverifikasi daerah nonaktif tidak muncul di daftar daerah dan me-return 404 jika diakses langsung.
     */
    public function test_inactive_region_is_hidden_and_returns_404(): void
    {
        $inactiveRegion = Region::create([
            'name' => 'Daerah Nonaktif Uji',
            'slug' => 'daerah-nonaktif-uji',
            'is_active' => false,
        ]);

        // Tidak muncul di index
        $indexResponse = $this->get(route('regions.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('Daerah Nonaktif Uji');

        // Return 404 di show
        $showResponse = $this->get(route('regions.show', $inactiveRegion->slug));
        $showResponse->assertStatus(404);

        $inactiveRegion->delete();
    }

    /**
     * Memverifikasi halaman detail daerah (regions.show) menampilkan destinasi aktif.
     */
    public function test_regions_show_displays_active_destinations(): void
    {
        $region = Region::where('slug', 'badung')->first();
        $this->assertNotNull($region);

        $response = $this->get(route('regions.show', $region->slug));

        $response->assertStatus(200);
        $response->assertSee('Badung');
        $response->assertSee('Kuta');
        $response->assertSee('Seminyak');
    }

    /**
     * Memverifikasi destinasi nonaktif tidak muncul di detail daerah.
     */
    public function test_inactive_destination_not_displayed_in_region_show(): void
    {
        $region = Region::where('slug', 'badung')->first();
        $inactiveDest = Destination::create([
            'region_id' => $region->id,
            'name' => 'Pantai Rahasia Nonaktif',
            'slug' => 'pantai-rahasia-nonaktif',
            'latitude' => -8.7000000,
            'longitude' => 115.1500000,
            'is_active' => false,
        ]);

        $response = $this->get(route('regions.show', $region->slug));
        $response->assertStatus(200);
        $response->assertDontSee('Pantai Rahasia Nonaktif');

        $inactiveDest->delete();
    }

    /**
     * Memverifikasi daerah tanpa destinasi menampilkan empty state yang informatif.
     */
    public function test_region_without_destinations_shows_empty_state(): void
    {
        $emptyRegion = Region::create([
            'name' => 'Jembrana',
            'slug' => 'jembrana',
            'is_active' => true,
        ]);

        $response = $this->get(route('regions.show', $emptyRegion->slug));

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Destinasi untuk Daerah Ini');

        $emptyRegion->delete();
    }

    /**
     * Memverifikasi halaman daftar kategori (categories.index) menampilkan kategori aktif.
     */
    public function test_categories_index_renders_active_categories(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Pantai');
        $response->assertSee('Alam');
        $response->assertSee('Budaya');
        $response->assertSee('Pura');
    }

    /**
     * Memverifikasi kategori nonaktif tidak muncul di index dan me-return 404 pada show.
     */
    public function test_inactive_category_is_hidden_and_returns_404(): void
    {
        $inactiveCat = Category::create([
            'name' => 'Kategori Nonaktif Uji',
            'slug' => 'kategori-nonaktif-uji',
            'is_active' => false,
        ]);

        // Tidak muncul di index
        $indexResponse = $this->get(route('categories.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('Kategori Nonaktif Uji');

        // Return 404 di show
        $showResponse = $this->get(route('categories.show', $inactiveCat->slug));
        $showResponse->assertStatus(404);

        $inactiveCat->delete();
    }

    /**
     * Memverifikasi halaman detail kategori (categories.show) menampilkan destinasi terkait.
     */
    public function test_categories_show_displays_active_destinations(): void
    {
        $category = Category::where('slug', 'pantai')->first();
        $this->assertNotNull($category);

        $response = $this->get(route('categories.show', $category->slug));

        $response->assertStatus(200);
        $response->assertSee('Pantai');
        $response->assertSee('Kuta');
        $response->assertSee('Seminyak');
    }

    /**
     * Memverifikasi kategori tanpa destinasi menampilkan empty state yang informatif.
     */
    public function test_category_without_destinations_shows_empty_state(): void
    {
        $emptyCat = Category::create([
            'name' => 'Olahraga Ekstrem',
            'slug' => 'olahraga-ekstrem',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.show', $emptyCat->slug));

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Destinasi untuk Kategori Ini');

        $emptyCat->delete();
    }

    /**
     * Memverifikasi slug daerah atau kategori yang tidak ditemukan mengembalikan 404.
     */
    public function test_non_existent_slug_returns_404(): void
    {
        $responseRegion = $this->get('/regions/slug-daerah-palsu-tidak-ada');
        $responseRegion->assertStatus(404);

        $responseCategory = $this->get('/categories/slug-kategori-palsu-tidak-ada');
        $responseCategory->assertStatus(404);
    }
}
