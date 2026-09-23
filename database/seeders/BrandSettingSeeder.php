<?php

namespace Database\Seeders;

use App\Models\BrandSetting;
use Illuminate\Database\Seeder;

class BrandSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Catatan Konfigurasi:
         * Data identitas brand di bawah merupakan konfigurasi awal (placeholder) yang disiapkan
         * agar website langsung memiliki identitas visual dasar, dan dapat diubah sepenuhnya
         * oleh admin melalui antarmuka pengaturan brand.
         */
        BrandSetting::updateOrCreate(
            ['brand_name' => 'Bali Tour Service'],
            [
                'tagline' => 'Jelajahi Bali dengan lebih mudah',
                'logo_path' => null,
                'primary_color' => '#0f172a',
                'secondary_color' => '#f59e0b',
                'whatsapp_number' => '6281234567890', // Nomor placeholder demo uji coba
                'contact_email' => 'info@balitourservice.local',
                'about_text' => 'Layanan sewa kendaraan dan panduan tour privat terpercaya di Bali dengan rute yang dapat dikustomisasi secara fleksibel.',
                'is_active' => true,
            ]
        );
    }
}
