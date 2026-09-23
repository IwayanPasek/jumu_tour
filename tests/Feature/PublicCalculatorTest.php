<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use Tests\TestCase;

class PublicCalculatorTest extends TestCase
{
    /**
     * Memverifikasi halaman perencana rute kalkulator dapat dibuka dengan sukses (200 OK).
     */
    public function test_calculator_page_can_be_rendered(): void
    {
        $response = $this->get(route('calculator.index'));

        $response->assertOk();
        $response->assertSee('Rencana Rute Perjalanan Bali');
        $response->assertSee('Titik Jemput (Pickup)');
        $response->assertSee('Bandara Internasional I Gusti Ngurah Rai (DPS)');
    }

    /**
     * Memverifikasi destinasi aktif dengan koordinat valid muncul sebagai pilihan rute.
     */
    public function test_active_destination_with_coordinates_appears_in_calculator_options(): void
    {
        $region = Region::create([
            'name' => 'Daerah Uji Rute',
            'slug' => 'daerah-uji-rute',
            'is_active' => true,
        ]);

        $activeDestination = Destination::create([
            'region_id' => $region->id,
            'name' => 'Pura Tirta Empul Tampaksiring',
            'slug' => 'pura-tirta-empul-tampaksiring',
            'latitude' => -8.4150,
            'longitude' => 115.3150,
            'is_active' => true,
        ]);

        $response = $this->get(route('calculator.index'));

        $response->assertOk();
        $response->assertSee('Pura Tirta Empul Tampaksiring');

        $activeDestination->delete();
        $region->delete();
    }

    /**
     * Memverifikasi destinasi nonaktif tidak muncul dalam pilihan rute kalkulator.
     */
    public function test_inactive_destination_does_not_appear_in_calculator_options(): void
    {
        $region = Region::create([
            'name' => 'Daerah Nonaktif Rute',
            'slug' => 'daerah-nonaktif-rute',
            'is_active' => true,
        ]);

        $inactiveDestination = Destination::create([
            'region_id' => $region->id,
            'name' => 'Wisata Tersembunyi Rahasia Nonaktif',
            'slug' => 'wisata-tersembunyi-rahasia-nonaktif',
            'latitude' => -8.3000,
            'longitude' => 115.2000,
            'is_active' => false,
        ]);

        $response = $this->get(route('calculator.index'));

        $response->assertOk();
        $response->assertDontSee('Wisata Tersembunyi Rahasia Nonaktif');

        $inactiveDestination->delete();
        $region->delete();
    }

    /**
     * Memverifikasi fallback peta tampil ketika kunci API browser kosong.
     */
    public function test_map_fallback_message_present_when_key_is_empty(): void
    {
        $response = $this->get(route('calculator.index'));

        $response->assertOk();
        $response->assertSee('Mode Rute Berbasis Daftar');
        $response->assertSee('Pratinjau peta Google Maps interaktif sedang tidak aktif');
    }

    /**
     * Memverifikasi kalkulator membatasi tampilan hanya untuk destinasi di daerah aktif.
     */
    public function test_destination_with_inactive_region_does_not_appear(): void
    {
        $inactiveRegion = Region::create([
            'name' => 'Wilayah Ditutup Rute',
            'slug' => 'wilayah-ditutup-rute',
            'is_active' => false,
        ]);

        $destination = Destination::create([
            'region_id' => $inactiveRegion->id,
            'name' => 'Wisata di Daerah Ditutup',
            'slug' => 'wisata-di-daerah-ditutup',
            'latitude' => -8.4000,
            'longitude' => 115.3000,
            'is_active' => true,
        ]);

        $response = $this->get(route('calculator.index'));

        $response->assertOk();
        $response->assertDontSee('Wisata di Daerah Ditutup');

        $destination->delete();
        $inactiveRegion->delete();
    }
}
