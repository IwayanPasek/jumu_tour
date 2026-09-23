<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use Tests\TestCase;

class PublicDestinationTest extends TestCase
{
    /**
     * Memverifikasi katalog destinasi publik dapat diakses dan menampilkan destinasi aktif.
     */
    public function test_public_destinations_index_renders_active_destinations(): void
    {
        $response = $this->get(route('destinations.index'));

        $response->assertOk();
        $response->assertSee(__('destinations.title'));
    }

    /**
     * Memverifikasi halaman detail tempat wisata publik menampilkan data lengkap.
     */
    public function test_public_destinations_show_renders_active_destination(): void
    {
        $destination = Destination::where('is_active', true)
            ->whereHas('region', fn($q) => $q->where('is_active', true))
            ->first();

        $this->assertNotNull($destination);

        $response = $this->get(route('destinations.show', $destination->slug));

        $response->assertOk();
        $response->assertSee($destination->name);
        $response->assertSee($destination->region->name);
        $response->assertSee((string) $destination->latitude);
        $response->assertSee((string) $destination->longitude);
    }

    /**
     * Memverifikasi destinasi wisata nonaktif menghasilkan 404 Not Found di halaman publik.
     */
    public function test_inactive_destination_returns_404_on_public_show(): void
    {
        $region = Region::create([
            'name' => 'Daerah Uji 404',
            'slug' => 'daerah-uji-404',
            'is_active' => true,
        ]);

        $inactiveDest = Destination::create([
            'region_id' => $region->id,
            'name' => 'Destinasi Nonaktif Uji',
            'slug' => 'destinasi-nonaktif-uji',
            'latitude' => -8.5000,
            'longitude' => 115.2000,
            'is_active' => false,
        ]);

        $response = $this->get(route('destinations.show', $inactiveDest->slug));
        $response->assertNotFound();

        $inactiveDest->delete();
        $region->delete();
    }

    /**
     * Memverifikasi destinasi di dalam daerah nonaktif tidak dapat diakses secara publik (404).
     */
    public function test_destination_with_inactive_region_returns_404_on_public_show(): void
    {
        $inactiveRegion = Region::create([
            'name' => 'Kawasan Ditutup',
            'slug' => 'kawasan-ditutup',
            'is_active' => false,
        ]);

        $destination = Destination::create([
            'region_id' => $inactiveRegion->id,
            'name' => 'Wisata di Kawasan Ditutup',
            'slug' => 'wisata-di-kawasan-ditutup',
            'latitude' => -8.5500,
            'longitude' => 115.2500,
            'is_active' => true,
        ]);

        $response = $this->get(route('destinations.show', $destination->slug));
        $response->assertNotFound();

        $destination->delete();
        $inactiveRegion->delete();
    }
}
