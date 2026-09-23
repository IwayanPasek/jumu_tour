<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateRouteRequest;
use App\Services\PricingService;
use App\Services\RouteService;
use Exception;
use Illuminate\Http\JsonResponse;

class RouteController extends Controller
{
    /**
     * Endpoint kalkulasi rute perjalanan tour dan estimasi biaya menggunakan Google Routes API & PricingService.
     */
    public function calculate(
        CalculateRouteRequest $request,
        RouteService $routeService,
        PricingService $pricingService
    ): JsonResponse {
        try {
            $pickup = $request->input('pickup');
            $destinationsInput = $request->input('destinations');
            $passengerCount = (int) $request->input('passenger_count', 1);
            $destinationIds = array_column($destinationsInput, 'id');

            // 1. Hitung rute jarak & durasi server-side
            $routeResult = $routeService->computeTourRoute($pickup, $destinationIds);

            // 2. Hitung estimasi biaya berdasarkan jarak riil dan jumlah penumpang
            $pricingResult = $pricingService->calculate(
                $routeResult['distance_kilometers'],
                $passengerCount
            );

            // 3. Kembalikan response gabungan terpadu
            return response()->json([
                'success' => true,
                'route' => $routeResult,
                'pricing' => $pricingResult,
                'message' => 'Estimasi rute dan biaya berhasil dihitung.',
                'disclaimer' => 'Harga ini merupakan estimasi awal dan dapat berubah berdasarkan kesepakatan layanan.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kendala saat menghitung estimasi. Silakan coba lagi.',
            ], 422);
        }
    }
}
