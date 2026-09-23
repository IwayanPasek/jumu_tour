<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    /**
     * Menampilkan halaman awal perencana rute tour dan peta interaktif.
     */
    public function index(): View
    {
        // Ambil destinasi aktif yang memiliki koordinat valid dan daerah aktif
        $destinations = Destination::query()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [-90, 90])
            ->whereBetween('longitude', [-180, 180])
            ->whereHas('region', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['region', 'category'])
            ->orderBy('display_order', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function (Destination $dest) {
                $imageUrl = null;
                if (!empty($dest->image_path)) {
                    if (Str::startsWith($dest->image_path, ['http://', 'https://'])) {
                        $imageUrl = $dest->image_path;
                    } elseif (Storage::disk('public')->exists($dest->image_path)) {
                        $imageUrl = Storage::url($dest->image_path);
                    } elseif (file_exists(public_path($dest->image_path))) {
                        $imageUrl = asset($dest->image_path);
                    }
                }

                return [
                    'id' => $dest->id,
                    'name' => $dest->name,
                    'slug' => $dest->slug,
                    'region' => $dest->region ? $dest->region->name : null,
                    'category' => $dest->category ? $dest->category->name : null,
                    'latitude' => (float) $dest->latitude,
                    'longitude' => (float) $dest->longitude,
                    'address' => $dest->address,
                    'image_url' => $imageUrl,
                ];
            });

        // Preset lokasi titik jemput populer di Bali
        $presetPickups = [
            [
                'id' => 'pickup-dps',
                'name' => 'Bandara Internasional I Gusti Ngurah Rai (DPS)',
                'address' => 'Tuban, Kuta, Kabupaten Badung, Bali',
                'latitude' => -8.7481,
                'longitude' => 115.1672,
                'type' => 'pickup',
            ],
            [
                'id' => 'pickup-kuta',
                'name' => 'Kuta Central (Pantai Kuta)',
                'address' => 'Jl. Pantai Kuta, Kuta, Badung, Bali',
                'latitude' => -8.7230,
                'longitude' => 115.1725,
                'type' => 'pickup',
            ],
            [
                'id' => 'pickup-seminyak',
                'name' => 'Seminyak Square',
                'address' => 'Jl. Kayu Aya No.1, Seminyak, Kuta, Badung, Bali',
                'latitude' => -8.6865,
                'longitude' => 115.1540,
                'type' => 'pickup',
            ],
            [
                'id' => 'pickup-ubud',
                'name' => 'Ubud Center (Puri Saren & Pasar Seni)',
                'address' => 'Jl. Raya Ubud, Ubud, Gianyar, Bali',
                'latitude' => -8.5069,
                'longitude' => 115.2625,
                'type' => 'pickup',
            ],
            [
                'id' => 'pickup-sanur',
                'name' => 'Pelabuhan Sanur (Matahari Terbit)',
                'address' => 'Sanur Kaja, Denpasar Selatan, Kota Denpasar, Bali',
                'latitude' => -8.6750,
                'longitude' => 115.2630,
                'type' => 'pickup',
            ],
            [
                'id' => 'pickup-nusa-dua',
                'name' => 'Kawasan Pariwisata Nusa Dua (ITDC)',
                'address' => 'Nusa Dua, Benoa, Kuta Selatan, Badung, Bali',
                'latitude' => -8.7990,
                'longitude' => 115.2280,
                'type' => 'pickup',
            ],
            [
                'id' => 'pickup-canggu',
                'name' => 'Canggu Center (Batu Bolong)',
                'address' => 'Jl. Pantai Batu Bolong, Canggu, Kuta Utara, Badung, Bali',
                'latitude' => -8.6500,
                'longitude' => 115.1320,
                'type' => 'pickup',
            ],
            [
                'id' => 'pickup-jimbaran',
                'name' => 'Pantai Muaya Jimbaran',
                'address' => 'Jimbaran, Kuta Selatan, Badung, Bali',
                'latitude' => -8.7750,
                'longitude' => 115.1667,
                'type' => 'pickup',
            ],
        ];

        $googleMapsBrowserKey = env('GOOGLE_MAPS_BROWSER_KEY') ?: env('GOOGLE_MAPS_API_KEY');

        return view('public.calculator.index', compact(
            'destinations',
            'presetPickups',
            'googleMapsBrowserKey'
        ));
    }
}
