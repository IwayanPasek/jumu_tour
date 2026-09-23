<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
                'name' => 'Badung',
                'regency' => 'Kabupaten Badung',
                'description' => 'Pusat pariwisata selatan Bali meliputi Kuta, Seminyak, Canggu, Jimbaran, Nusa Dua, dan Uluwatu.',
            ],
            [
                'name' => 'Gianyar',
                'regency' => 'Kabupaten Gianyar',
                'description' => 'Pusat seni, budaya, tradisi, dan alam persawahan hijau dengan ikon utama Ubud dan Tegallalang.',
            ],
            [
                'name' => 'Bangli',
                'regency' => 'Kabupaten Bangli',
                'description' => 'Satu-satunya daerah di Bali tanpa laut, terkenal dengan pemandangan Gunung Batur Kintamani dan desa tradisional Penglipuran.',
            ],
            [
                'name' => 'Tabanan',
                'regency' => 'Kabupaten Tabanan',
                'description' => 'Lumbung beras Bali yang terkenal dengan Pura Tanah Lot, Pura Ulun Danu Beratan Bedugul, dan terasering Jatiluwih.',
            ],
            [
                'name' => 'Karangasem',
                'regency' => 'Kabupaten Karangasem',
                'description' => 'Kawasan Bali Timur dengan pesona Pura Besakih, Taman Air Tirta Gangga, dan Istana Air Ujung.',
            ],
            [
                'name' => 'Buleleng',
                'regency' => 'Kabupaten Buleleng',
                'description' => 'Kawasan Bali Utara dengan wisata pantai lumba-lumba Lovina serta air terjun eksotis.',
            ],
            [
                'name' => 'Klungkung',
                'regency' => 'Kabupaten Klungkung',
                'description' => 'Daerah bersejarah Kerta Gosa serta kepulauan Nusa Penida dan Nusa Lembongan.',
            ],
            [
                'name' => 'Denpasar',
                'regency' => 'Kota Denpasar',
                'description' => 'Ibu kota Provinsi Bali dengan kawasan wisata Sanur, museum budaya, dan kuliner khas.',
            ],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['slug' => Str::slug($region['name'])],
                [
                    'name' => $region['name'],
                    'regency' => $region['regency'],
                    'description' => $region['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
