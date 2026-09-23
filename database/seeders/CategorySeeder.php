<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pantai',
                'description' => 'Wisata pesisir pantai pasir putih, sunset, selancar, dan aktivitas bahari.',
            ],
            [
                'name' => 'Alam',
                'description' => 'Wisata pemandangan perbukitan, gunung, danau, dan terasering sawah.',
            ],
            [
                'name' => 'Budaya',
                'description' => 'Desa adat, tari-tarian tradisional, seni lukis, dan museum sejarah.',
            ],
            [
                'name' => 'Pura',
                'description' => 'Tempat peribadatan suci umat Hindu Bali dengan arsitektur kuno nan megah.',
            ],
            [
                'name' => 'Air Terjun',
                'description' => 'Air terjun alami tersembunyi di tengah hutan tropis yang sejuk dan asri.',
            ],
            [
                'name' => 'Kuliner',
                'description' => 'Restoran, kafe hits dengan pemandangan alam, serta hidangan kuliner khas Bali.',
            ],
            [
                'name' => 'Belanja',
                'description' => 'Pasar seni tradisional dan sentra oleh-oleh kerajinan khas Bali.',
            ],
            [
                'name' => 'Keluarga',
                'description' => 'Destinasi rekreasi ramah anak, kebun binatang, dan taman bermain keluarga.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
