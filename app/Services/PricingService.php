<?php

namespace App\Services;

use App\Models\PricingConfiguration;
use App\Support\MoneyFormatter;
use Exception;
use Illuminate\Support\Facades\Log;

class PricingService
{
    /**
     * Menghitung estimasi biaya perjalanan berdasarkan jarak riil dan jumlah penumpang.
     *
     * @param float $distanceKilometers Jarak tempuh dalam kilometer dari RouteService.
     * @param int $passengerCount Jumlah penumpang (default: 1).
     * @param array $options Opsi tambahan internal (opsional).
     * @return array Rincian kalkulasi harga terstruktur.
     * @throws Exception Jika konfigurasi tarif tidak valid atau tidak ditemukan.
     */
    public function calculate(float $distanceKilometers, int $passengerCount = 1, array $options = []): array
    {
        // 1. Ambil konfigurasi tarif aktif yang berlaku
        $config = PricingConfiguration::getActive();

        if (!$config) {
            Log::warning('PricingService: Tidak ditemukan PricingConfiguration yang aktif.');
            throw new Exception('Estimasi biaya belum dapat dihitung karena tarif belum dikonfigurasi.');
        }

        // 2. Sanitasi & Validasi input internal
        $distance = max(0.0, $distanceKilometers);
        $passengers = max(1, $passengerCount);

        // 3. Ekstrak nilai komponen tarif dari model
        $basePrice = (float) $config->base_price;
        $pricePerKm = (float) $config->price_per_kilometer;
        $extraPassengerPrice = (float) $config->extra_passenger_price;
        $parkingFee = (float) ($options['parking_fee'] ?? $config->parking_fee ?? 0);
        $tollFee = (float) ($options['toll_fee'] ?? $config->toll_fee ?? 0);
        $nightServiceFee = (float) ($options['night_service_fee'] ?? 0);
        $minimumPrice = (float) $config->minimum_price;

        // 4. Hitung rincian biaya sesuai formula resmi
        // a. Biaya dasar
        $baseCost = $basePrice;

        // b. Biaya jarak perjalanan (jarak riil x tarif per km)
        $distanceCost = round($distance * $pricePerKm, 2);

        // c. Biaya penumpang tambahan (penumpang pertama termasuk dalam base price)
        $extraPassengers = max($passengers - 1, 0);
        $passengerCost = round($extraPassengers * $extraPassengerPrice, 2);

        // d. Biaya tambahan (parkir, tol, layanan malam)
        $additionalCost = round($parkingFee + $tollFee + $nightServiceFee, 2);

        // e. Subtotal sebelum evaluasi minimum price
        $subtotal = round($baseCost + $distanceCost + $passengerCost + $additionalCost, 2);

        // f. Penerapan batas minimum price
        $minimumPriceApplied = false;
        $calculatedTotal = $subtotal;

        if ($calculatedTotal < $minimumPrice) {
            $calculatedTotal = $minimumPrice;
            $minimumPriceApplied = true;
        }

        // g. Pembulatan tahap akhir ke kelipatan Rp1.000 terdekat ke atas (ceil)
        $estimatedTotal = (int) (ceil($calculatedTotal / 1000) * 1000);

        // 5. Kembalikan representasi terstruktur
        return [
            'currency' => 'IDR',
            'base_cost' => (int) round($baseCost),
            'distance_cost' => (int) round($distanceCost),
            'passenger_cost' => (int) round($passengerCost),
            'parking_cost' => (int) round($parkingFee),
            'toll_cost' => (int) round($tollFee),
            'night_service_cost' => (int) round($nightServiceFee),
            'subtotal' => (int) round($subtotal),
            'minimum_price_applied' => $minimumPriceApplied,
            'minimum_price' => (int) round($minimumPrice),
            'estimated_total' => $estimatedTotal,
            'formatted_total' => MoneyFormatter::formatIdr($estimatedTotal),
            'formatted_base_cost' => MoneyFormatter::formatIdr($baseCost),
            'formatted_distance_cost' => MoneyFormatter::formatIdr($distanceCost),
            'formatted_passenger_cost' => MoneyFormatter::formatIdr($passengerCost),
            'rate_details' => [
                'package_name' => $config->name,
                'price_per_kilometer' => (int) round($pricePerKm),
                'extra_passenger_price' => (int) round($extraPassengerPrice),
                'formatted_price_per_km' => MoneyFormatter::formatIdr($pricePerKm),
            ],
        ];
    }
}
