<?php

namespace App\Services;

use App\Models\Destination;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RouteService
{
    /**
     * Endpoint resmi Google Routes API Compute Routes
     */
    protected const ROUTES_API_URL = 'https://routes.googleapis.com/directions/v2:computeRoutes';

    /**
     * Menghitung rute dari titik penjemputan ke satu atau beberapa destinasi wisata.
     *
     * @param array $pickup Data titik penjemputan [name, latitude, longitude, place_id]
     * @param array $destinationIds Urutan ID destinasi dari input pengguna
     * @return array Data terstruktur hasil perhitungan rute
     * @throws Exception
     */
    public function computeTourRoute(array $pickup, array $destinationIds): array
    {
        // 1. Ambil server API Key dari konfigurasi
        $serverApiKey = config('services.google.server_key') ?: (env('GOOGLE_MAPS_SERVER_KEY') ?: (env('GOOGLE_ROUTES_API_KEY') ?: env('GOOGLE_MAPS_API_KEY')));

        if (empty($serverApiKey)) {
            throw new Exception('Layanan peta sedang tidak tersedia. Konfigurasi server belum lengkap.');
        }

        // 2. Ambil data destinasi dari database dan jaga urutan aslinya
        $destinationsFromDb = Destination::whereIn('id', $destinationIds)
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->keyBy('id');

        $orderedDestinations = [];
        foreach ($destinationIds as $id) {
            if (!isset($destinationsFromDb[$id])) {
                throw new Exception('Salah satu lokasi destinasi tidak valid atau telah dinonaktifkan.');
            }
            $orderedDestinations[] = $destinationsFromDb[$id];
        }

        if (empty($orderedDestinations)) {
            throw new Exception('Destinasi tujuan tidak valid.');
        }

        // 3. Bangun payload request untuk Google Routes API
        $payload = $this->buildComputeRoutesPayload($pickup, $orderedDestinations);

        // 4. FieldMask spesifik sesuai aturan Google Routes API
        $fieldMask = implode(',', [
            'routes.distanceMeters',
            'routes.duration',
            'routes.polyline.encodedPolyline',
            'routes.legs.distanceMeters',
            'routes.legs.duration',
        ]);

        try {
            $response = Http::timeout(10)
                ->retry(2, 200, function ($exception) {
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                }, throw: false)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Goog-Api-Key' => $serverApiKey,
                    'X-Goog-FieldMask' => $fieldMask,
                ])
                ->post(self::ROUTES_API_URL, $payload);

            if ($response->failed()) {
                Log::warning('Google Routes API request failed', [
                    'status' => $response->status(),
                    'error_code' => $response->json('error.code'),
                    'error_message' => $response->json('error.message'),
                ]);

                throw new Exception('Rute belum dapat dihitung dari server peta. Silakan coba sesaat lagi.');
            }

            $data = $response->json();

            if (empty($data['routes']) || !isset($data['routes'][0])) {
                throw new Exception('Rute jalan raya tidak ditemukan untuk titik-titik lokasi yang dipilih.');
            }

            return $this->formatInternalRouteResponse($data['routes'][0], $pickup, $orderedDestinations);

        } catch (\Illuminate\Http\Client\RequestException | \Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('Google Routes API HTTP/Network Exception: ' . $e->getMessage());
            throw new Exception('Rute belum dapat dihitung dari server peta. Silakan coba sesaat lagi.');
        } catch (Exception $e) {
            if ($e->getMessage() === 'Layanan peta sedang tidak tersedia. Konfigurasi server belum lengkap.' ||
                $e->getMessage() === 'Salah satu lokasi destinasi tidak valid atau telah dinonaktifkan.' ||
                $e->getMessage() === 'Rute jalan raya tidak ditemukan untuk titik-titik lokasi yang dipilih.' ||
                $e->getMessage() === 'Rute belum dapat dihitung dari server peta. Silakan coba sesaat lagi.') {
                throw $e;
            }

            Log::error('RouteService computeTourRoute Exception: ' . $e->getMessage());
            throw new Exception('Terjadi kendala saat menghitung rute perjalanan. Silakan periksa kembali lokasi Anda.');
        }
    }

    /**
     * Membangun payload JSON untuk request Google Compute Routes.
     */
    protected function buildComputeRoutesPayload(array $pickup, array $destinations): array
    {
        // Origin selalu titik penjemputan
        $origin = [
            'location' => [
                'latLng' => [
                    'latitude' => (float) $pickup['latitude'],
                    'longitude' => (float) $pickup['longitude'],
                ],
            ],
        ];

        // Tujuan akhir selalu elemen destinasi terakhir
        $lastDest = end($destinations);
        $destination = [
            'location' => [
                'latLng' => [
                    'latitude' => (float) $lastDest->latitude,
                    'longitude' => (float) $lastDest->longitude,
                ],
            ],
        ];

        // Titik perantara (intermediates) jika ada lebih dari 1 destinasi
        $intermediates = [];
        $totalDest = count($destinations);
        if ($totalDest > 1) {
            for ($i = 0; $i < $totalDest - 1; $i++) {
                $intermediates[] = [
                    'location' => [
                        'latLng' => [
                            'latitude' => (float) $destinations[$i]->latitude,
                            'longitude' => (float) $destinations[$i]->longitude,
                        ],
                    ],
                ];
            }
        }

        $payload = [
            'origin' => $origin,
            'destination' => $destination,
            'travelMode' => 'DRIVE',
            'routingPreference' => 'TRAFFIC_UNAWARE',
        ];

        if (!empty($intermediates)) {
            $payload['intermediates'] = $intermediates;
        }

        return $payload;
    }

    /**
     * Mengonversi format Google Routes API menjadi format respon internal aplikasi.
     */
    protected function formatInternalRouteResponse(array $googleRoute, array $pickup, array $destinations): array
    {
        $totalDistanceMeters = (int) ($googleRoute['distanceMeters'] ?? 0);
        $totalDurationSeconds = $this->parseDurationSeconds($googleRoute['duration'] ?? '0s');
        $encodedPolyline = $googleRoute['polyline']['encodedPolyline'] ?? null;

        // Susun titik perjalanan urut: [pickup, dest1, dest2, ..., destN]
        $waypointSequence = [];
        $waypointSequence[] = [
            'name' => $pickup['name'] ?? 'Titik Penjemputan',
            'latitude' => (float) $pickup['latitude'],
            'longitude' => (float) $pickup['longitude'],
        ];
        foreach ($destinations as $dest) {
            $waypointSequence[] = [
                'name' => $dest->name,
                'latitude' => (float) $dest->latitude,
                'longitude' => (float) $dest->longitude,
            ];
        }

        // Petakan legs/segmen
        $legs = [];
        $rawLegs = $googleRoute['legs'] ?? [];
        foreach ($rawLegs as $index => $rawLeg) {
            $fromWaypoint = $waypointSequence[$index] ?? ['name' => 'Lokasi Asal', 'latitude' => 0, 'longitude' => 0];
            $toWaypoint = $waypointSequence[$index + 1] ?? ['name' => 'Lokasi Tujuan', 'latitude' => 0, 'longitude' => 0];

            $legDistanceMeters = (int) ($rawLeg['distanceMeters'] ?? 0);
            $legDurationSeconds = $this->parseDurationSeconds($rawLeg['duration'] ?? '0s');

            $legs[] = [
                'from' => $fromWaypoint,
                'to' => $toWaypoint,
                'distance_meters' => $legDistanceMeters,
                'distance_kilometers' => round($legDistanceMeters / 1000, 2),
                'duration_seconds' => $legDurationSeconds,
                'duration_text' => $this->formatDurationText($legDurationSeconds),
            ];
        }

        return [
            'distance_meters' => $totalDistanceMeters,
            'distance_kilometers' => round($totalDistanceMeters / 1000, 2),
            'duration_seconds' => $totalDurationSeconds,
            'duration_text' => $this->formatDurationText($totalDurationSeconds),
            'encoded_polyline' => $encodedPolyline,
            'legs' => $legs,
        ];
    }

    /**
     * Mem-parsing string durasi Google (contoh: "3600s" atau "720.5s") menjadi integer detik.
     */
    protected function parseDurationSeconds(string $duration): int
    {
        $durationString = rtrim($duration, 's');
        return (int) round((float) $durationString);
    }

    /**
     * Memformat detik menjadi teks jam & menit dalam Bahasa Indonesia yang ramah.
     */
    protected function formatDurationText(int $seconds): string
    {
        if ($seconds < 60) {
            return 'Kurang dari 1 menit';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return $minutes > 0 ? "{$hours} jam {$minutes} menit" : "{$hours} jam";
        }

        return "{$minutes} menit";
    }
}
