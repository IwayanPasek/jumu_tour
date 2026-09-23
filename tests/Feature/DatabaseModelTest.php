<?php

namespace Tests\Feature;

use App\Models\BrandSetting;
use App\Models\Category;
use App\Models\Destination;
use App\Models\PricingConfiguration;
use App\Models\Region;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class DatabaseModelTest extends TestCase
{
    /**
     * Memastikan data seeder awal tersedia di database.
     */
    public function test_seed_data_exists(): void
    {
        $this->assertGreaterThanOrEqual(8, Region::count());
        $this->assertGreaterThanOrEqual(8, Category::count());
        $this->assertGreaterThanOrEqual(15, Destination::count());
        $this->assertNotNull(PricingConfiguration::getActive());
        $this->assertNotNull(BrandSetting::getActive());
    }

    /**
     * Memverifikasi relasi Eloquent antara Destination, Region, dan Category.
     */
    public function test_relationships_work(): void
    {
        $destination = Destination::first();
        $this->assertNotNull($destination);
        $this->assertInstanceOf(Region::class, $destination->region);
        
        $region = Region::where('slug', 'badung')->first();
        $this->assertTrue($region->destinations()->exists());

        $category = Category::where('slug', 'pantai')->first();
        $this->assertTrue($category->destinations()->exists());
    }

    /**
     * Memverifikasi bahwa destinasi dengan category_id = null tetap valid.
     */
    public function test_destination_with_nullable_category_is_valid(): void
    {
        $region = Region::first();
        $destination = Destination::create([
            'region_id' => $region->id,
            'category_id' => null,
            'name' => 'Destinasi Tanpa Kategori Uji',
            'slug' => 'destinasi-tanpa-kategori-uji',
            'latitude' => -8.5000000,
            'longitude' => 115.2000000,
            'is_active' => true,
        ]);

        $this->assertNotNull($destination->id);
        $this->assertNull($destination->category);

        // Bersihkan data uji
        $destination->delete();
    }

    /**
     * Memverifikasi aturan restrictOnDelete: region tidak bisa dihapus jika masih punya destinasi.
     */
    public function test_region_cannot_be_deleted_if_has_destinations(): void
    {
        $regionWithDest = Region::whereHas('destinations')->first();
        $this->assertNotNull($regionWithDest);

        $this->expectException(QueryException::class);
        $regionWithDest->delete();
    }

    /**
     * Memverifikasi keunikan slug pada tabel-tabel utama.
     */
    public function test_slug_uniqueness(): void
    {
        $existingRegion = Region::first();
        $this->expectException(QueryException::class);
        
        Region::create([
            'name' => 'Duplikat Region',
            'slug' => $existingRegion->slug,
        ]);
    }

    /**
     * Memverifikasi presisi koordinat desimal.
     */
    public function test_coordinate_precision(): void
    {
        $kuta = Destination::where('slug', 'kuta')->first();
        $this->assertNotNull($kuta);
        $this->assertEquals('-8.7185000', (string) $kuta->latitude);
        $this->assertEquals('115.1686000', (string) $kuta->longitude);
    }

    /**
     * Memverifikasi bahwa hanya konfigurasi aktif terbaru yang dipilih.
     */
    public function test_active_configurations(): void
    {
        $activePricing = PricingConfiguration::getActive();
        $this->assertNotNull($activePricing);
        $this->assertTrue($activePricing->is_active);
        $this->assertEquals('Tarif Default MVP', $activePricing->name);

        $activeBrand = BrandSetting::getActive();
        $this->assertNotNull($activeBrand);
        $this->assertTrue($activeBrand->is_active);
        $this->assertEquals('Bali Tour Service', $activeBrand->brand_name);
    }
}
