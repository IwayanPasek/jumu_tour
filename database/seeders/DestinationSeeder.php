<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cache region dan category mapping berdasarkan slug
        $regions = Region::pluck('id', 'slug');
        $categories = Category::pluck('id', 'slug');

        /**
         * Catatan Data:
         * Koordinat di bawah adalah perkiraan titik lokasi awal untuk keperluan demo/testing MVP,
         * bukan koordinat resmi bersertifikasi. Tidak menggunakan gambar eksternal berhak cipta.
         */
        $destinations = [
            [
                'name' => 'Kuta',
                'region_slug' => 'badung',
                'category_slug' => 'pantai',
                'description' => 'Pantai legendaris di Bali dengan ombak selancar populer dan pemandangan matahari terbenam.',
                'address' => 'Jl. Pantai Kuta, Kuta, Kabupaten Badung, Bali',
                'latitude' => -8.7185000,
                'longitude' => 115.1686000,
                'display_order' => 1,
            ],
            [
                'name' => 'Seminyak',
                'region_slug' => 'badung',
                'category_slug' => 'pantai',
                'description' => 'Kawasan pesisir trendi dengan deretan beach club, butik, dan panorama sunset elegan.',
                'address' => 'Seminyak, Kuta, Kabupaten Badung, Bali',
                'latitude' => -8.6913000,
                'longitude' => 115.1582000,
                'display_order' => 2,
            ],
            [
                'name' => 'Canggu',
                'region_slug' => 'badung',
                'category_slug' => 'pantai',
                'description' => 'Destinasi favorit ekspatriat dan peselancar dengan atmosfer santai serta kafe-kafe estetik.',
                'address' => 'Canggu, Kuta Utara, Kabupaten Badung, Bali',
                'latitude' => -8.6478000,
                'longitude' => 115.1385000,
                'display_order' => 3,
            ],
            [
                'name' => 'Tanah Lot',
                'region_slug' => 'tabanan',
                'category_slug' => 'pura',
                'description' => 'Pura laut ikonik di atas bongkahan batu karang besar dengan latar siluet sunset spektakuler.',
                'address' => 'Beraban, Kediri, Kabupaten Tabanan, Bali',
                'latitude' => -8.6212000,
                'longitude' => 115.0868000,
                'display_order' => 4,
            ],
            [
                'name' => 'Uluwatu',
                'region_slug' => 'badung',
                'category_slug' => 'pura',
                'description' => 'Pura Luhur di atas tebing karang terjal setinggi 70 meter menghadap Samudra Hindia dengan pertunjukan Tari Kecak.',
                'address' => 'Pecatu, Kuta Selatan, Kabupaten Badung, Bali',
                'latitude' => -8.8291000,
                'longitude' => 115.0849000,
                'display_order' => 5,
            ],
            [
                'name' => 'Nusa Dua',
                'region_slug' => 'badung',
                'category_slug' => 'pantai',
                'description' => 'Kawasan resor bintang lima eksklusif dengan pantai pasir putih tenang dan atraksi Waterblow.',
                'address' => 'Benoa, Kuta Selatan, Kabupaten Badung, Bali',
                'latitude' => -8.8005000,
                'longitude' => 115.2289000,
                'display_order' => 6,
            ],
            [
                'name' => 'Ubud',
                'region_slug' => 'gianyar',
                'category_slug' => 'budaya',
                'description' => 'Jantung kebudayaan Bali dengan Puri Saren Agung, Monkey Forest, galeri seni, dan yoga sanctuary.',
                'address' => 'Jl. Raya Ubud, Kecamatan Ubud, Kabupaten Gianyar, Bali',
                'latitude' => -8.5069000,
                'longitude' => 115.2625000,
                'display_order' => 7,
            ],
            [
                'name' => 'Tegallalang',
                'region_slug' => 'gianyar',
                'category_slug' => 'alam',
                'description' => 'Terasering persawahan berundak bertingkat indah dengan pemandangan lembah tropis asri.',
                'address' => 'Jl. Raya Tegallalang, Kabupaten Gianyar, Bali',
                'latitude' => -8.4344000,
                'longitude' => 115.2777000,
                'display_order' => 8,
            ],
            [
                'name' => 'Kintamani',
                'region_slug' => 'bangli',
                'category_slug' => 'alam',
                'description' => 'Kawasan dataran tinggi sejuk dengan pemandangan dramatis Gunung Batur dan Danau Batur.',
                'address' => 'Penelokan, Kintamani, Kabupaten Bangli, Bali',
                'latitude' => -8.2435000,
                'longitude' => 115.3342000,
                'display_order' => 9,
            ],
            [
                'name' => 'Penglipuran',
                'region_slug' => 'bangli',
                'category_slug' => 'budaya',
                'description' => 'Desa adat terbersih di dunia dengan arsitektur rumah tradisional bambu yang seragam dan lestari.',
                'address' => 'Kubu, Kecamatan Bangli, Kabupaten Bangli, Bali',
                'latitude' => -8.4527000,
                'longitude' => 115.3575000,
                'display_order' => 10,
            ],
            [
                'name' => 'Bedugul',
                'region_slug' => 'tabanan',
                'category_slug' => 'alam',
                'description' => 'Kawasan danau sejuk di pegunungan dengan Pura Ulun Danu Beratan yang tampak mengapung di atas air.',
                'address' => 'Candikuning, Baturiti, Kabupaten Tabanan, Bali',
                'latitude' => -8.2752000,
                'longitude' => 115.1654000,
                'display_order' => 11,
            ],
            [
                'name' => 'Jatiluwih',
                'region_slug' => 'tabanan',
                'category_slug' => 'alam',
                'description' => 'Warisan Budaya Dunia UNESCO berupa hamparan terasering persawahan terluas dengan sistem irigasi Subak.',
                'address' => 'Jatiluwih, Penebel, Kabupaten Tabanan, Bali',
                'latitude' => -8.3705000,
                'longitude' => 115.1311000,
                'display_order' => 12,
            ],
            [
                'name' => 'Lovina',
                'region_slug' => 'buleleng',
                'category_slug' => 'pantai',
                'description' => 'Pantai pasir hitam tenang di Bali Utara yang terkenal dengan atraksi melihat lumba-lumba liar saat fajar.',
                'address' => 'Kalibukbuk, Kecamatan Buleleng, Kabupaten Buleleng, Bali',
                'latitude' => -8.1610000,
                'longitude' => 115.0270000,
                'display_order' => 13,
            ],
            [
                'name' => 'Tirta Gangga',
                'region_slug' => 'karangasem',
                'category_slug' => 'budaya',
                'description' => 'Taman air bekas istana Kerajaan Karangasem dengan kolam air jernih, jembatan batu, dan patung bersejarah.',
                'address' => 'Ababi, Abang, Kabupaten Karangasem, Bali',
                'latitude' => -8.4122000,
                'longitude' => 115.5875000,
                'display_order' => 14,
            ],
            [
                'name' => 'Sanur',
                'region_slug' => 'denpasar',
                'category_slug' => 'pantai',
                'description' => 'Pantai berpasir putih dengan ombak tenang yang terkenal sebagai tempat terbaik menikmati sunrise di Bali.',
                'address' => 'Sanur, Denpasar Selatan, Kota Denpasar, Bali',
                'latitude' => -8.6882000,
                'longitude' => 115.2625000,
                'display_order' => 15,
            ],
        ];

        foreach ($destinations as $dest) {
            $regionId = $regions[$dest['region_slug']] ?? null;
            $categoryId = $categories[$dest['category_slug']] ?? null;

            if ($regionId) {
                Destination::updateOrCreate(
                    ['slug' => Str::slug($dest['name'])],
                    [
                        'region_id' => $regionId,
                        'category_id' => $categoryId,
                        'name' => $dest['name'],
                        'description' => $dest['description'],
                        'address' => $dest['address'],
                        'latitude' => $dest['latitude'],
                        'longitude' => $dest['longitude'],
                        'image_path' => null,
                        'is_active' => true,
                        'display_order' => $dest['display_order'],
                    ]
                );
            }
        }
    }
}
