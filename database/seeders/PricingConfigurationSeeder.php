<?php

namespace Database\Seeders;

use App\Models\PricingConfiguration;
use Illuminate\Database\Seeder;

class PricingConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Catatan Bisnis:
         * Nilai tarif di bawah ini adalah placeholder contoh untuk kebutuhan pengujian sistem MVP.
         * Nilai ini bukan harga final bisnis dan dapat disesuaikan sewaktu-waktu oleh pemilik bisnis
         * melalui modul pengaturan tarif admin.
         */
        PricingConfiguration::updateOrCreate(
            ['name' => 'Tarif Default MVP'],
            [
                'base_price' => 550000.00,             // Contoh tarif dasar sewa mobil + pengemudi 10 jam (IDR)
                'price_per_kilometer' => 3500.00,      // Contoh tarif per km tambahan (IDR)
                'extra_passenger_price' => 50000.00,   // Contoh biaya per penumpang tambahan (IDR)
                'parking_fee' => 10000.00,             // Contoh estimasi alokasi parkir (IDR)
                'toll_fee' => 15000.00,                // Contoh estimasi alokasi tol (IDR)
                'night_service_fee' => 50000.00,       // Contoh biaya layanan malam/overtime (IDR)
                'minimum_price' => 500000.00,          // Contoh harga batas bawah minimum order (IDR)
                'is_active' => true,
            ]
        );
    }
}
