<?php

namespace App\Services;

use App\Models\BrandSetting;
use Exception;

class WhatsAppService
{
    /**
     * Normalisasi nomor telepon ke format internasional (E.164 tanpa tanda plus).
     * Contoh:
     * - "08123456789" -> "628123456789"
     * - "+62 812-3456-7890" -> "6281234567890"
     * - "(0812) 345678" -> "62812345678"
     *
     * @param string|null $phoneNumber
     * @param string $defaultCountryCode
     * @return string
     * @throws Exception Jika nomor kosong atau tidak valid
     */
    public function normalizePhoneNumber(?string $phoneNumber, string $defaultCountryCode = '62'): string
    {
        if (empty($phoneNumber)) {
            throw new Exception('Nomor telepon tidak boleh kosong.');
        }

        // Hapus karakter non-digit kecuali tanda plus di awal
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (empty($cleaned)) {
            throw new Exception('Nomor telepon tidak valid.');
        }

        // Jika diawali 0, ganti dengan kode negara default (62)
        if (str_starts_with($cleaned, '0')) {
            $cleaned = $defaultCountryCode . substr($cleaned, 1);
        }

        // Validasi panjang minimum nomor telepon (minimal 9 digit internasional)
        if (strlen($cleaned) < 9 || strlen($cleaned) > 16) {
            throw new Exception('Panjang nomor telepon tidak valid untuk format WhatsApp.');
        }

        return $cleaned;
    }

    /**
     * Membangun URL WhatsApp Click-to-Chat resmi (https://wa.me/{phone}?text={encoded_message}).
     *
     * @param string $recipientPhone Nomor tujuan WhatsApp (misal: nomor bisnis admin)
     * @param string $message Teks pesan yang akan dikirim
     * @return string
     * @throws Exception
     */
    public function generateClickToChatUrl(string $recipientPhone, string $message): string
    {
        $normalizedPhone = $this->normalizePhoneNumber($recipientPhone);

        // Bersihkan pesan dari karakter berbahaya/HTML tags dan trim
        $sanitizedMessage = strip_tags(trim($message));

        // URL encode teks pesan sesuai standar RFC 3986
        $encodedText = rawurlencode($sanitizedMessage);

        return "https://wa.me/{$normalizedPhone}?text={$encodedText}";
    }

    /**
     * Membangun template pesan pemesanan tour yang rapi, informatif, dan bilingual (EN / ID).
     *
     * @param array $bookingData Data pemesanan (nama, tanggal, pickup, tujuan, rincian biaya, dsb)
     * @param string|null $locale Kode bahasa ('en' atau 'id', default membaca App::getLocale())
     * @return string
     */
    public function formatTourBookingMessage(array $bookingData, ?string $locale = null): string
    {
        $currentLocale = $locale ?: \Illuminate\Support\Facades\App::getLocale();
        if (! in_array($currentLocale, ['en', 'id'], true)) {
            $currentLocale = 'en';
        }

        $brand = BrandSetting::getActive();
        $brandName = $brand ? $brand->brand_name : 'Bali Tour Service';

        $customerName = strip_tags($bookingData['customer_name'] ?? ($currentLocale === 'id' ? 'Pelanggan' : 'Customer'));
        $tourDate = strip_tags($bookingData['tour_date'] ?? date('d-m-Y'));
        $passengers = (int) ($bookingData['passenger_count'] ?? 1);
        $pickupName = strip_tags($bookingData['pickup_name'] ?? '-');
        $destinations = (array) ($bookingData['destinations'] ?? []);
        $distanceKm = $bookingData['distance_km'] ?? '0';
        $durationText = strip_tags($bookingData['duration_text'] ?? '-');
        $estimatedPrice = strip_tags($bookingData['formatted_price'] ?? 'Rp0');
        $notes = !empty($bookingData['notes']) ? strip_tags($bookingData['notes']) : '-';

        $destinationsList = '';
        foreach ($destinations as $index => $dest) {
            $num = $index + 1;
            $destName = is_array($dest) ? ($dest['name'] ?? ($currentLocale === 'id' ? 'Tujuan ' . $num : 'Stop ' . $num)) : (string) $dest;
            $destinationsList .= "  {$num}. " . strip_tags($destName) . "\n";
        }

        if ($currentLocale === 'en') {
            $message = "Hello {$brandName}, I would like to book a private tour with the following details:\n\n";
            $message .= "👤 Customer Details: {$customerName}\n";
            $message .= "📅 Travel Date: {$tourDate}\n";
            $message .= "👥 Number of Passengers: {$passengers} Person(s)\n";
            $message .= "📍 Pickup Location: {$pickupName}\n\n";
            $message .= "🗺️ Planned Destinations:\n";
            $message .= $destinationsList . "\n";
            $message .= "🚗 Total Distance: {$distanceKm} km\n";
            $message .= "⏱️ Estimated Duration: {$durationText}\n";
            $message .= "💰 Estimated Price: {$estimatedPrice}\n";
            $message .= "📝 Additional Notes: {$notes}\n\n";
            $message .= "Note: Final price will be confirmed via WhatsApp based on schedule and vehicle availability. Thank you!";
        } else {
            $message = "Halo {$brandName}, saya ingin melakukan reservasi sewa tour Bali dengan rincian berikut:\n\n";
            $message .= "👤 Data Pelanggan: {$customerName}\n";
            $message .= "📅 Tanggal Perjalanan: {$tourDate}\n";
            $message .= "👥 Jumlah Penumpang: {$passengers} Orang\n";
            $message .= "📍 Lokasi Penjemputan: {$pickupName}\n\n";
            $message .= "🗺️ Tujuan Perjalanan:\n";
            $message .= $destinationsList . "\n";
            $message .= "🚗 Total Jarak: {$distanceKm} km\n";
            $message .= "⏱️ Detail Estimasi Perjalanan: {$durationText}\n";
            $message .= "💰 Estimasi Biaya: {$estimatedPrice}\n";
            $message .= "📝 Catatan Tambahan: {$notes}\n\n";
            $message .= "Catatan: Harga final akan dikonfirmasi melalui WhatsApp. Terima kasih!";
        }

        return $message;
    }

    /**
     * Mendapatkan nomor WhatsApp bisnis aktif dari BrandSetting atau env fallback.
     *
     * @return string
     * @throws Exception Jika nomor belum dikonfigurasi
     */
    public function getBusinessWhatsAppNumber(): string
    {
        $brand = BrandSetting::getActive();
        $number = $brand?->whatsapp_number ?: env('WHATSAPP_PHONE_NUMBER');

        if (empty($number)) {
            throw new Exception('Nomor WhatsApp bisnis belum dikonfigurasi dalam sistem.');
        }

        return $this->normalizePhoneNumber($number);
    }
}
