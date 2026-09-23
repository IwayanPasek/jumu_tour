<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Destination;
use App\Services\PricingService;
use App\Services\RouteService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /**
     * Memproses permohonan reservasi tour dan membuat link WhatsApp Click-to-Chat.
     * Tidak menyimpan data ke database transaksi (stateless booking link generator).
     */
    public function generateWhatsAppLink(
        BookingRequest $request,
        RouteService $routeService,
        PricingService $pricingService,
        WhatsAppService $whatsAppService
    ): JsonResponse {
        try {
            $validated = $request->validated();

            $pickup = $validated['pickup'];
            $destinationsInput = $validated['destinations'];
            $passengerCount = (int) $validated['passenger_count'];
            $destinationIds = array_column($destinationsInput, 'id');

            // 1. Hitung rute jarak & durasi di server (tidak mempercayai client)
            $routeResult = $routeService->computeTourRoute($pickup, $destinationIds);

            // 2. Hitung harga di server (tidak mempercayai client)
            $pricingResult = $pricingService->calculate(
                $routeResult['distance_kilometers'],
                $passengerCount
            );

            // 3. Ambil nama-nama destinasi urut dari database
            $destinationsMap = Destination::whereIn('id', $destinationIds)->get()->keyBy('id');
            $orderedDestinations = [];
            foreach ($destinationIds as $id) {
                if (isset($destinationsMap[$id])) {
                    $orderedDestinations[] = [
                        'id' => $id,
                        'name' => $destinationsMap[$id]->name,
                    ];
                }
            }

            // 4. Dapatkan nomor WhatsApp bisnis resmi
            $businessPhone = $whatsAppService->getBusinessWhatsAppNumber();

            // 5. Susun pesan format booking aman
            $bookingMessage = $whatsAppService->formatTourBookingMessage([
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'tour_date' => $validated['tour_date'],
                'passenger_count' => $passengerCount,
                'pickup_name' => $pickup['name'],
                'destinations' => $orderedDestinations,
                'distance_km' => $routeResult['distance_kilometers'],
                'duration_text' => $routeResult['duration_text'],
                'formatted_price' => $pricingResult['formatted_total'],
                'notes' => $validated['notes'] ?? '',
            ]);

            // 6. Buat URL wa.me
            $whatsappUrl = $whatsAppService->generateClickToChatUrl($businessPhone, $bookingMessage);

            return response()->json([
                'success' => true,
                'whatsapp_url' => $whatsappUrl,
                'message' => 'Tautan pemesanan WhatsApp berhasil dibuat.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kendala saat menyusun permohonan reservasi WhatsApp.',
            ], 422);
        }
    }
}
