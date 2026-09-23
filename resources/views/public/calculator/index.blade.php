@extends('layouts.app')

@section('title', 'Kalkulator Rute & Peta Wisata Bali - ' . ($brand['name'] ?? 'Bali Tour Service'))
@section('meta_description', 'Rencanakan rute liburan Anda di Bali, pilih titik jemput dan hingga 5 tempat wisata favorit dengan perhitungan jarak dan durasi Google Routes API.')

@section('content')
<!-- Page Header -->
<section class="bg-brand-primary text-white py-4 py-md-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active text-brand-secondary" aria-current="page">Kalkulator Rute Wisata</li>
            </ol>
        </nav>
        <h1 class="fw-bold mb-2 display-6">Rencana Rute Perjalanan Bali</h1>
        <p class="text-white-50 mb-0 lead fs-6">
            Pilih titik jemput dan tentukan hingga 5 destinasi wisata impian Anda untuk menghitung total jarak dan estimasi durasi rute.
        </p>
    </div>
</section>

<!-- Main Calculator Section -->
<section class="py-4 py-lg-5 bg-light">
    <div class="container">
        <!-- Notification Banner / Alert Placeholder -->
        <div id="calculator-alert-container"></div>

        <div class="row g-4">
            <!-- Left Column: Controls, Destination Selection & Route Result -->
            <div class="col-lg-5 col-xl-5">
                <!-- 1. Titik Jemput (Pickup Location) Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <span class="badge rounded-circle p-2 bg-success text-white" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;">P</span>
                                <span>Titik Jemput (Pickup)</span>
                            </h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small" id="pickup-status-badge">
                                <i class="bi bi-check-circle me-1"></i>Siap
                            </span>
                        </div>

                        <!-- Pickup Mode Tabs -->
                        <div class="btn-group w-100 mb-3" role="group" aria-label="Mode Pemilihan Titik Jemput">
                            <input type="radio" class="btn-check" name="pickup_mode" id="mode_preset" value="preset" checked autocomplete="off">
                            <label class="btn btn-outline-primary btn-sm" for="mode_preset">Lokasi Populer Bali</label>

                            <input type="radio" class="btn-check" name="pickup_mode" id="mode_manual" value="manual" autocomplete="off">
                            <label class="btn btn-outline-secondary btn-sm" for="mode_manual">Input Koordinat (Dev)</label>
                        </div>

                        <!-- Mode 1: Preset Dropdown -->
                        <div id="section-pickup-preset">
                            <label for="pickup_preset_select" class="form-label small fw-semibold text-muted">
                                Pilih Lokasi Penjemputan:
                            </label>
                            <select id="pickup_preset_select" class="form-select" aria-label="Pilih lokasi jemput">
                                @foreach($presetPickups as $index => $preset)
                                    <option value="{{ $index }}" {{ $index === 0 ? 'selected' : '' }}>
                                        {{ $preset['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small mt-1" id="pickup-preset-address">
                                {{ $presetPickups[0]['address'] }}
                            </div>
                        </div>

                        <!-- Mode 2: Manual Coordinates (Development Testing) -->
                        <div id="section-pickup-manual" class="d-none">
                            <div class="alert alert-warning py-2 px-3 small mb-3">
                                <i class="bi bi-cone-striped me-1"></i> Mode pengujian koordinat manual.
                            </div>
                            <div class="mb-2">
                                <label for="manual_pickup_name" class="form-label small fw-semibold text-muted">Nama Lokasi Jemput:</label>
                                <input type="text" id="manual_pickup_name" class="form-control form-control-sm" placeholder="Contoh: Hotel Villa Seminyak">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label for="manual_pickup_lat" class="form-label small fw-semibold text-muted">Latitude:</label>
                                    <input type="number" step="any" id="manual_pickup_lat" class="form-control form-control-sm font-monospace" placeholder="-8.7481">
                                </div>
                                <div class="col-6">
                                    <label for="manual_pickup_lng" class="form-label small fw-semibold text-muted">Longitude:</label>
                                    <input type="number" step="any" id="manual_pickup_lng" class="form-control form-control-sm font-monospace" placeholder="115.1672">
                                </div>
                            </div>
                            <button type="button" id="btn-apply-manual-pickup" class="btn btn-sm btn-outline-primary w-100" aria-label="Terapkan koordinat manual titik jemput">
                                Terapkan Titik Jemput
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Tambah Destinasi Wisata Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-pin-map-fill text-danger"></i>
                                <span>Pilih Tempat Wisata</span>
                            </h5>
                            <span class="badge bg-light text-secondary border small px-2 py-1" id="destination-count-badge">
                                <span id="selected-dest-count">0</span> / 5 Tujuan
                            </span>
                        </div>

                        @if($destinations->count() > 0)
                            <label for="destination_select" class="form-label small fw-semibold text-muted">
                                Cari dan Tambahkan ke Rute:
                            </label>
                            <div class="input-group mb-2">
                                <select id="destination_select" class="form-select" aria-label="Pilih destinasi wisata">
                                    <option value="">-- Pilih Tempat Wisata --</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest['id'] }}">
                                            {{ $dest['name'] }} ({{ $dest['region'] ?: 'Bali' }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" id="btn-add-destination" class="btn btn-brand-primary d-inline-flex align-items-center gap-1" aria-label="Tambahkan tempat wisata terpilih">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Tambah</span>
                                </button>
                            </div>
                            <div class="form-text small">
                                Anda dapat memilih hingga maksimal 5 tujuan wisata dalam satu rencana perjalanan.
                            </div>
                        @else
                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <i class="bi bi-info-circle me-1"></i> Belum ada tempat wisata aktif dengan koordinat valid. Silakan hubungi admin.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 3. Daftar Urutan Tujuan Perjalanan Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                Runtutan Perjalanan (<span id="route-items-count">0</span> Tujuan)
                            </h6>
                            <button type="button" id="btn-clear-all" class="btn btn-link text-danger text-decoration-none p-0 small" style="font-size: 0.8rem;" aria-label="Kosongkan seluruh daftar rute tujuan">
                                Kosongkan Rute
                            </button>
                        </div>

                        <!-- Dynamic Destination List Container -->
                        <div id="destinations-list" class="d-flex flex-column gap-2 mb-3" aria-live="polite">
                            <!-- Empty State -->
                            <div id="destinations-empty-state" class="text-center py-4 px-2 border rounded-3 bg-light">
                                <i class="bi bi-compass display-6 text-muted opacity-50 mb-2 d-block"></i>
                                <span class="text-dark fw-semibold small d-block">Belum ada tujuan wisata dipilih</span>
                                <span class="text-muted" style="font-size: 0.75rem;">Pilih tempat wisata dari daftar di atas untuk menambahkan rute perjalanan.</span>
                            </div>
                        </div>

                        <!-- Passenger Count Selector -->
                        <div class="mb-3 pt-2 border-top">
                            <label for="passenger_count" class="form-label small fw-semibold text-dark d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-people-fill text-brand-primary me-1"></i> Jumlah Penumpang:</span>
                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.75rem;">1-12 Orang</span>
                            </label>
                            <select id="passenger_count" class="form-select form-select-sm" aria-label="Pilih jumlah penumpang">
                                <option value="1" selected>1 Penumpang (Termasuk Biaya Dasar)</option>
                                <option value="2">2 Penumpang (+1 Ekstra)</option>
                                <option value="3">3 Penumpang (+2 Ekstra)</option>
                                <option value="4">4 Penumpang (+3 Ekstra)</option>
                                <option value="5">5 Penumpang (+4 Ekstra)</option>
                                <option value="6">6 Penumpang (+5 Ekstra)</option>
                                <option value="7">7 Penumpang (+6 Ekstra)</option>
                                <option value="8">8 Penumpang (+7 Ekstra)</option>
                                <option value="9">9 Penumpang (+8 Ekstra)</option>
                                <option value="10">10 Penumpang (+9 Ekstra)</option>
                                <option value="11">11 Penumpang (+10 Ekstra)</option>
                                <option value="12">12 Penumpang (+11 Ekstra)</option>
                            </select>
                            <div class="form-text small" style="font-size: 0.75rem;">
                                Penumpang pertama tercakup dalam tarif dasar. Penumpang ke-2 dan seterusnya dikenakan tarif ekstra per orang.
                            </div>
                        </div>

                        <!-- Calculate Route Action Button -->
                        <button type="button" 
                                id="btn-calculate-route" 
                                class="btn btn-brand-primary w-100 py-2 rounded-pill d-inline-flex align-items-center justify-content-center gap-2 fw-semibold shadow-sm"
                                disabled>
                            <i class="bi bi-arrow-repeat spin d-none" id="calculate-spinner"></i>
                            <span id="calculate-btn-text">Hitung Rute & Estimasi Biaya</span>
                            <i class="bi bi-arrow-right" id="calculate-arrow-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- 4. Hasil Perhitungan Rute & Estimasi Biaya (Route & Pricing Result Card) -->
                <div id="route-result-container" class="card border-0 shadow-sm rounded-4 mb-4 bg-white d-none">
                    <div class="card-header bg-white border-bottom p-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Hasil Rute & Estimasi Biaya</span>
                            </h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                Rute & Tarif Aktif
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Summary Stats (Jarak & Durasi) -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <span class="text-muted small d-block mb-1">Total Jarak Tempuh</span>
                                    <h4 class="fw-bold text-brand-primary mb-0" id="res-total-distance">0 km</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <span class="text-muted small d-block mb-1">Estimasi Durasi</span>
                                    <h4 class="fw-bold text-brand-primary mb-0" id="res-total-duration">0 m</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Card Estimasi Biaya (Pricing Card) -->
                        <div class="card border border-primary-subtle rounded-3 bg-light mb-4 overflow-hidden">
                            <div class="card-header bg-primary text-white py-2 px-3 d-flex align-items-center justify-content-between">
                                <span class="fw-semibold small d-flex align-items-center gap-1">
                                    <i class="bi bi-cash-stack"></i> Estimasi Biaya Sewa Tour
                                </span>
                                <span class="badge bg-white text-primary small">Estimasi Awal</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex align-items-baseline justify-content-between mb-3 border-bottom pb-2">
                                    <span class="text-dark fw-bold">Total Estimasi:</span>
                                    <h3 class="fw-bold text-brand-primary mb-0" id="res-pricing-total">Rp0</h3>
                                </div>

                                <!-- Rincian Perhitungan Biaya -->
                                <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    Rincian Perhitungan Tarif:
                                </h6>
                                <div class="d-flex flex-column gap-1 small text-secondary mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Biaya Dasar Armada (<span id="res-package-name">Paket Standar</span>):</span>
                                        <span class="fw-semibold text-dark" id="res-base-cost">Rp0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Biaya Jarak Tempuh (<span id="res-distance-calc-desc">0 km</span>):</span>
                                        <span class="fw-semibold text-dark" id="res-distance-cost">Rp0</span>
                                    </div>
                                    <div class="d-flex justify-content-between" id="res-passenger-row">
                                        <span>Biaya Penumpang Ekstra (<span id="res-passenger-calc-desc">0 orang</span>):</span>
                                        <span class="fw-semibold text-dark" id="res-passenger-cost">Rp0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Biaya Parkir & Tol:</span>
                                        <span class="fw-semibold text-dark" id="res-additional-cost">Rp0</span>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-1 mt-1 fw-semibold text-dark">
                                        <span>Subtotal Biaya:</span>
                                        <span id="res-subtotal-cost">Rp0</span>
                                    </div>
                                </div>

                                <!-- Minimum Price Alert (Conditional) -->
                                <div id="res-minimum-price-notice" class="alert alert-warning py-1 px-2 small mb-2 d-none" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle-fill me-1"></i> Berlaku ketentuan <strong>Tarif Minimum Layanan</strong> (<span id="res-minimum-price-val">Rp0</span>).
                                </div>

                                <!-- Disclaimer Resmi -->
                                <p class="text-muted mb-0 small" style="font-size: 0.75rem; line-height: 1.4;" id="res-pricing-disclaimer">
                                    <i class="bi bi-shield-check me-1"></i> Harga ini merupakan estimasi awal dan dapat berubah berdasarkan kesepakatan layanan.
                                </p>
                            </div>
                        </div>

                        <!-- Per-Leg / Segmen Details -->
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wider">
                            Rincian Segmen Perjalanan:
                        </h6>
                        <div id="res-legs-list" class="d-flex flex-column gap-2 mb-3">
                            <!-- Populated by JS -->
                        </div>

                        <!-- Booking Placeholder Button (Cluster 10 Batasan: Belum ada form booking/WhatsApp) -->
                        <div class="p-3 bg-light rounded-3 border text-center mt-3">
                            <button type="button" class="btn btn-outline-secondary w-100 rounded-pill py-2 disabled" aria-disabled="true">
                                <i class="bi bi-calendar2-check me-1"></i> Lanjut Pemesanan <span class="badge bg-secondary ms-1">Segera Hadir</span>
                            </button>
                            <span class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                                Fitur pemesanan online dan konfirmasi WhatsApp akan dibuka pada tahap selanjutnya.
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Google Map -->
            <div class="col-lg-7 col-xl-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white sticky-lg-top" style="top: 85px; z-index: 10;">
                    <!-- Map Header Info Bar -->
                    <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-map-fill text-brand-primary fs-5"></i>
                            <span class="fw-bold text-dark small">Peta Rute Interaktif</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 small">
                            <span class="badge bg-light text-dark border">
                                <span class="text-success fw-bold">P</span> = Jemput
                            </span>
                            <span class="badge bg-light text-dark border">
                                <span class="text-danger fw-bold">1..5</span> = Wisata
                            </span>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div class="position-relative">
                        <div id="calculator-map" 
                             class="calculator-map-container"
                             style="width: 100%; background-color: #f1f5f9;"
                             role="region" 
                             aria-label="Peta rute interaktif titik jemput dan tempat wisata di Bali">
                        </div>

                        <!-- Fallback Alert Box (jika API Key tidak ada atau Maps gagal dimuat) -->
                        <div id="map-fallback-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-none d-flex flex-column align-items-center justify-content-center p-4 bg-light text-center" style="z-index: 20;">
                            <div class="card border-0 shadow-sm p-4 rounded-4" style="max-width: 480px;">
                                <div class="mb-3 text-brand-secondary">
                                    <i class="bi bi-geo-alt-fill display-4"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Mode Rute Berbasis Daftar</h5>
                                <p class="text-muted small mb-3 lh-base">
                                    Pratinjau peta Google Maps interaktif sedang tidak aktif (kunci API browser belum dikonfigurasi). 
                                    Anda tetap dapat menghitung total jarak dan estimasi durasi melalui panel di samping.
                                </p>
                                <div class="p-3 bg-light rounded text-start small border">
                                    <div class="fw-semibold text-dark mb-1">Status Rute:</div>
                                    <div id="fallback-route-summary" class="text-muted">
                                        Titik jemput siap, silakan tentukan tempat wisata.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Footer Instructions -->
                    <div class="card-footer bg-white border-top p-3 small text-muted d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>
                            <i class="bi bi-info-circle me-1"></i> Jalur polyline rute jalan raya akan tergambar setelah Anda menekan tombol "Hitung Rute".
                        </span>
                        <button type="button" id="btn-reset-map-view" class="btn btn-link text-decoration-none p-0 small text-brand-primary">
                            <i class="bi bi-arrows-fullscreen me-1"></i>Pusatkan Peta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Styling tombol & list item urutan */
    .destination-item {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .destination-item:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
    }
    .dest-order-badge {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .btn-order-action {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }
    .spin {
        animation: spin 1s infinite linear;
    }
    @keyframes spin {
        from { transform: scale(1) rotate(0deg); }
        to { transform: scale(1) rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    // 1. Data Inisialisasi dari Controller Laravel
    const availableDestinations = @json($destinations);
    const presetPickups = @json($presetPickups);
    const googleMapsBrowserKey = "{{ $googleMapsBrowserKey }}";
    const csrfToken = "{{ csrf_token() }}";
    const calculateRouteUrl = "{{ route('calculator.route') }}";

    // 2. State Aplikasi Frontend
    const state = {
        pickupLocation: presetPickups.length > 0 ? { ...presetPickups[0] } : null,
        selectedDestinations: [], // array of destination objects
        maxDestinations: 5,
        map: null,
        polyline: null,
        markers: {
            pickup: null,
            destinations: []
        },
        mapReady: false,
        isCalculating: false
    };

    // 3. Elemen DOM
    const pickupPresetSelect = document.getElementById('pickup_preset_select');
    const pickupPresetAddress = document.getElementById('pickup-preset-address');
    const modePresetRadio = document.getElementById('mode_preset');
    const modeManualRadio = document.getElementById('mode_manual');
    const sectionPreset = document.getElementById('section-pickup-preset');
    const sectionManual = document.getElementById('section-pickup-manual');
    const btnApplyManualPickup = document.getElementById('btn-apply-manual-pickup');
    const manualPickupName = document.getElementById('manual_pickup_name');
    const manualPickupLat = document.getElementById('manual_pickup_lat');
    const manualPickupLng = document.getElementById('manual_pickup_lng');

    const destinationSelect = document.getElementById('destination_select');
    const btnAddDestination = document.getElementById('btn-add-destination');
    const destinationsList = document.getElementById('destinations-list');
    const destinationsEmptyState = document.getElementById('destinations-empty-state');
    const selectedDestCountEl = document.getElementById('selected-dest-count');
    const routeItemsCountEl = document.getElementById('route-items-count');
    const btnClearAll = document.getElementById('btn-clear-all');
    const btnCalculateRoute = document.getElementById('btn-calculate-route');
    const calculateSpinner = document.getElementById('calculate-spinner');
    const calculateBtnText = document.getElementById('calculate-btn-text');
    const calculateArrowIcon = document.getElementById('calculate-arrow-icon');
    const btnResetMapView = document.getElementById('btn-reset-map-view');

    const routeResultContainer = document.getElementById('route-result-container');
    const resTotalDistance = document.getElementById('res-total-distance');
    const resTotalDuration = document.getElementById('res-total-duration');
    const resLegsList = document.getElementById('res-legs-list');

    // Elemen DOM Estimasi Biaya (Cluster 10)
    const passengerCountSelect = document.getElementById('passenger_count');
    const resPricingTotal = document.getElementById('res-pricing-total');
    const resPackageName = document.getElementById('res-package-name');
    const resBaseCost = document.getElementById('res-base-cost');
    const resDistanceCalcDesc = document.getElementById('res-distance-calc-desc');
    const resDistanceCost = document.getElementById('res-distance-cost');
    const resPassengerRow = document.getElementById('res-passenger-row');
    const resPassengerCalcDesc = document.getElementById('res-passenger-calc-desc');
    const resPassengerCost = document.getElementById('res-passenger-cost');
    const resAdditionalCost = document.getElementById('res-additional-cost');
    const resSubtotalCost = document.getElementById('res-subtotal-cost');
    const resMinimumPriceNotice = document.getElementById('res-minimum-price-notice');
    const resMinimumPriceVal = document.getElementById('res-minimum-price-val');
    const resPricingDisclaimer = document.getElementById('res-pricing-disclaimer');

    const mapContainer = document.getElementById('calculator-map');
    const mapFallbackOverlay = document.getElementById('map-fallback-overlay');
    const fallbackRouteSummary = document.getElementById('fallback-route-summary');
    const alertContainer = document.getElementById('calculator-alert-container');

    // 4. Utility Notification Alert
    function showAlert(message, type = 'warning') {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    function clearAlert() {
        alertContainer.innerHTML = '';
    }

    // 5. Inisialisasi Titik Jemput (Pickup)
    function setPickupLocation(location) {
        if (!location || isNaN(location.latitude) || isNaN(location.longitude)) {
            showAlert('Koordinat titik jemput tidak valid. Silakan periksa kembali.', 'danger');
            return;
        }

        const lat = parseFloat(location.latitude);
        const lng = parseFloat(location.longitude);

        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            showAlert('Nilai koordinat berada di luar rentang geografis yang valid.', 'danger');
            return;
        }

        state.pickupLocation = {
            id: location.id || 'pickup-custom',
            name: location.name || 'Titik Jemput Kustom',
            address: location.address || null,
            latitude: lat,
            longitude: lng,
            type: 'pickup'
        };

        // Reset previous calculation & polyline when pickup changes
        resetCalculatedRoute();

        updatePickupMarker();
        updateUIState();
        fitMapBounds();
    }

    modePresetRadio.addEventListener('change', function() {
        if (this.checked) {
            sectionPreset.classList.remove('d-none');
            sectionManual.classList.add('d-none');
            const idx = parseInt(pickupPresetSelect.value, 10);
            if (presetPickups[idx]) {
                setPickupLocation(presetPickups[idx]);
                pickupPresetAddress.textContent = presetPickups[idx].address || '';
            }
        }
    });

    modeManualRadio.addEventListener('change', function() {
        if (this.checked) {
            sectionPreset.classList.add('d-none');
            sectionManual.classList.remove('d-none');
        }
    });

    pickupPresetSelect.addEventListener('change', function() {
        const idx = parseInt(this.value, 10);
        if (presetPickups[idx]) {
            setPickupLocation(presetPickups[idx]);
            pickupPresetAddress.textContent = presetPickups[idx].address || '';
        }
    });

    btnApplyManualPickup.addEventListener('click', function() {
        const name = manualPickupName.value.trim() || 'Titik Jemput Kustom';
        const lat = parseFloat(manualPickupLat.value);
        const lng = parseFloat(manualPickupLng.value);

        if (isNaN(lat) || isNaN(lng)) {
            showAlert('Harap masukkan nilai latitude dan longitude berupa angka valid.', 'warning');
            return;
        }

        setPickupLocation({
            id: 'manual-pickup-' + Date.now(),
            name: name,
            address: 'Koordinat manual: ' + lat.toFixed(4) + ', ' + lng.toFixed(4),
            latitude: lat,
            longitude: lng,
            type: 'pickup'
        });

        showAlert(`Titik jemput "${name}" berhasil diterapkan.`, 'success');
    });

    // 6. Manajemen Destinasi Terpilih
    function addDestination(destId) {
        if (!destId) return;

        if (state.selectedDestinations.length >= state.maxDestinations) {
            showAlert(`Batas maksimal ${state.maxDestinations} destinasi telah tercapai. Hapus salah satu untuk menggantinya.`, 'warning');
            return;
        }

        const isDuplicate = state.selectedDestinations.some(d => String(d.id) === String(destId));
        if (isDuplicate) {
            showAlert('Tempat wisata ini sudah ada dalam daftar rute Anda.', 'info');
            return;
        }

        const destObj = availableDestinations.find(d => String(d.id) === String(destId));
        if (!destObj) {
            showAlert('Tempat wisata tidak ditemukan dalam database.', 'danger');
            return;
        }

        state.selectedDestinations.push({
            id: destObj.id,
            name: destObj.name,
            slug: destObj.slug,
            region: destObj.region,
            category: destObj.category,
            address: destObj.address,
            latitude: parseFloat(destObj.latitude),
            longitude: parseFloat(destObj.longitude),
            type: 'destination'
        });

        destinationSelect.value = '';
        resetCalculatedRoute();

        renderDestinationsList();
        updateDestinationMarkers();
        updateUIState();
        fitMapBounds();
    }

    function removeDestination(index) {
        if (index < 0 || index >= state.selectedDestinations.length) return;
        state.selectedDestinations.splice(index, 1);
        resetCalculatedRoute();
        renderDestinationsList();
        updateDestinationMarkers();
        updateUIState();
        fitMapBounds();
    }

    function moveDestinationUp(index) {
        if (index <= 0) return;
        const temp = state.selectedDestinations[index];
        state.selectedDestinations[index] = state.selectedDestinations[index - 1];
        state.selectedDestinations[index - 1] = temp;
        resetCalculatedRoute();
        renderDestinationsList();
        updateDestinationMarkers();
        fitMapBounds();
    }

    function moveDestinationDown(index) {
        if (index >= state.selectedDestinations.length - 1) return;
        const temp = state.selectedDestinations[index];
        state.selectedDestinations[index] = state.selectedDestinations[index + 1];
        state.selectedDestinations[index + 1] = temp;
        resetCalculatedRoute();
        renderDestinationsList();
        updateDestinationMarkers();
        fitMapBounds();
    }

    function clearAllDestinations() {
        if (state.selectedDestinations.length === 0) return;
        if (confirm('Kosongkan seluruh destinasi dalam daftar rute?')) {
            state.selectedDestinations = [];
            resetCalculatedRoute();
            renderDestinationsList();
            updateDestinationMarkers();
            updateUIState();
            fitMapBounds();
        }
    }

    function resetCalculatedRoute() {
        // Hapus polyline lama dari peta
        if (state.polyline) {
            state.polyline.setMap(null);
            state.polyline = null;
        }
        // Sembunyikan container hasil
        routeResultContainer.classList.add('d-none');
    }

    // 7. Render Antarmuka Daftar Destinasi
    function renderDestinationsList() {
        destinationsList.innerHTML = '';

        if (state.selectedDestinations.length === 0) {
            destinationsList.appendChild(destinationsEmptyState);
            destinationsEmptyState.classList.remove('d-none');
            return;
        }

        destinationsEmptyState.classList.add('d-none');

        state.selectedDestinations.forEach((dest, index) => {
            const itemEl = document.createElement('div');
            itemEl.className = 'destination-item p-3 rounded-3 bg-white shadow-xs d-flex align-items-center justify-content-between gap-2';

            const isFirst = index === 0;
            const isLast = index === state.selectedDestinations.length - 1;

            itemEl.innerHTML = `
                <div class="d-flex align-items-center gap-3 overflow-hidden">
                    <span class="dest-order-badge badge rounded-circle bg-brand-primary text-white">
                        ${index + 1}
                    </span>
                    <div class="text-truncate">
                        <strong class="d-block text-dark small text-truncate">${escapeHtml(dest.name)}</strong>
                        <span class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-geo-alt text-danger me-1"></i>${escapeHtml(dest.region || 'Bali')}
                            ${dest.category ? ' &bull; ' + escapeHtml(dest.category) : ''}
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <button type="button" 
                            class="btn btn-outline-secondary btn-order-action btn-move-up" 
                            data-index="${index}" 
                            ${isFirst ? 'disabled' : ''} 
                            aria-label="Pindahkan tujuan ${index + 1} ke atas">
                        <i class="bi bi-arrow-up-short fs-5"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-outline-secondary btn-order-action btn-move-down" 
                            data-index="${index}" 
                            ${isLast ? 'disabled' : ''} 
                            aria-label="Pindahkan tujuan ${index + 1} ke bawah">
                        <i class="bi bi-arrow-down-short fs-5"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-outline-danger btn-order-action btn-remove-dest" 
                            data-index="${index}" 
                            aria-label="Hapus destinasi ${escapeHtml(dest.name)}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

            destinationsList.appendChild(itemEl);
        });

        destinationsList.querySelectorAll('.btn-move-up').forEach(btn => {
            btn.addEventListener('click', function() {
                moveDestinationUp(parseInt(this.getAttribute('data-index'), 10));
            });
        });

        destinationsList.querySelectorAll('.btn-move-down').forEach(btn => {
            btn.addEventListener('click', function() {
                moveDestinationDown(parseInt(this.getAttribute('data-index'), 10));
            });
        });

        destinationsList.querySelectorAll('.btn-remove-dest').forEach(btn => {
            btn.addEventListener('click', function() {
                removeDestination(parseInt(this.getAttribute('data-index'), 10));
            });
        });
    }

    function updateUIState() {
        const count = state.selectedDestinations.length;
        selectedDestCountEl.textContent = count;
        routeItemsCountEl.textContent = count;

        if (count >= state.maxDestinations) {
            btnAddDestination.disabled = true;
            destinationSelect.disabled = true;
        } else {
            btnAddDestination.disabled = false;
            destinationSelect.disabled = false;
        }

        const canProceed = Boolean(state.pickupLocation && count >= 1 && !state.isCalculating);
        btnCalculateRoute.disabled = !canProceed;

        if (fallbackRouteSummary) {
            if (count === 0) {
                fallbackRouteSummary.textContent = `Titik jemput: ${state.pickupLocation ? state.pickupLocation.name : '-'}, belum ada destinasi yang dipilih.`;
            } else {
                const listNames = state.selectedDestinations.map((d, i) => `${i + 1}. ${d.name}`).join(' → ');
                fallbackRouteSummary.innerHTML = `<strong>Jemput:</strong> ${escapeHtml(state.pickupLocation.name)}<br><strong>Urutan Wisata:</strong> ${escapeHtml(listNames)}`;
            }
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // 8. Integrasi Peta Google Maps JS API & Polyline
    window.initCalculatorMap = function() {
        try {
            const baliCenter = { lat: -8.409518, lng: 115.188916 };

            state.map = new google.maps.Map(mapContainer, {
                center: baliCenter,
                zoom: 10,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                styles: [
                    { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'on' }] }
                ]
            });

            state.mapReady = true;

            updatePickupMarker();
            updateDestinationMarkers();
            fitMapBounds();
        } catch (e) {
            console.warn('Google Maps initialization fallback triggered:', e);
            activateMapFallback();
        }
    };

    function activateMapFallback() {
        state.mapReady = false;
        if (mapFallbackOverlay) {
            mapFallbackOverlay.classList.remove('d-none');
        }
    }

    window.gm_authFailure = function() {
        console.warn('Google Maps authentication failure detected.');
        activateMapFallback();
    };

    function updatePickupMarker() {
        if (!state.mapReady || !state.map) return;

        if (state.markers.pickup) {
            state.markers.pickup.setMap(null);
            state.markers.pickup = null;
        }

        if (state.pickupLocation) {
            state.markers.pickup = new google.maps.Marker({
                position: { lat: state.pickupLocation.latitude, lng: state.pickupLocation.longitude },
                map: state.map,
                title: `Titik Jemput: ${state.pickupLocation.name}`,
                label: {
                    text: 'P',
                    color: '#ffffff',
                    fontWeight: 'bold'
                },
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 16,
                    fillColor: '#10b981',
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: '#ffffff'
                }
            });
        }
    }

    function updateDestinationMarkers() {
        if (!state.mapReady || !state.map) return;

        state.markers.destinations.forEach(m => m.setMap(null));
        state.markers.destinations = [];

        state.selectedDestinations.forEach((dest, index) => {
            const marker = new google.maps.Marker({
                position: { lat: dest.latitude, lng: dest.longitude },
                map: state.map,
                title: `${index + 1}. ${dest.name}`,
                label: {
                    text: String(index + 1),
                    color: '#ffffff',
                    fontWeight: 'bold'
                },
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 14,
                    fillColor: '#0f172a',
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: '#f59e0b'
                }
            });

            state.markers.destinations.push(marker);
        });
    }

    function fitMapBounds() {
        if (!state.mapReady || !state.map) return;

        const allPoints = [];
        if (state.pickupLocation) {
            allPoints.push({ lat: state.pickupLocation.latitude, lng: state.pickupLocation.longitude });
        }
        state.selectedDestinations.forEach(d => {
            allPoints.push({ lat: d.latitude, lng: d.longitude });
        });

        if (allPoints.length === 0) {
            state.map.setCenter({ lat: -8.409518, lng: 115.188916 });
            state.map.setZoom(10);
            return;
        }

        if (allPoints.length === 1) {
            state.map.setCenter(allPoints[0]);
            state.map.setZoom(13);
            return;
        }

        const bounds = new google.maps.LatLngBounds();
        allPoints.forEach(pt => bounds.extend(pt));
        state.map.fitBounds(bounds);
    }

    // 9. Eksekusi Request Hitung Rute (Google Routes API via Server Backend)
    async function calculateTourRoute() {
        if (!state.pickupLocation || state.selectedDestinations.length === 0) {
            showAlert('Pastikan titik jemput telah ditentukan dan minimal ada 1 tempat wisata yang dipilih.', 'warning');
            return;
        }

        clearAlert();
        state.isCalculating = true;

        // UI Loading State
        btnCalculateRoute.disabled = true;
        calculateSpinner.classList.remove('d-none');
        calculateArrowIcon.classList.add('d-none');
        calculateBtnText.textContent = 'Menghitung Rute & Estimasi...';

        const passengerCount = parseInt(passengerCountSelect.value, 10) || 1;

        const payload = {
            pickup: {
                name: state.pickupLocation.name,
                latitude: state.pickupLocation.latitude,
                longitude: state.pickupLocation.longitude,
                place_id: state.pickupLocation.place_id || null
            },
            destinations: state.selectedDestinations.map(d => ({
                id: d.id
            })),
            passenger_count: passengerCount
        };

        try {
            const response = await fetch(calculateRouteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                const errorMsg = data.message || 'Gagal menghitung rute perjalanan. Silakan periksa kembali lokasi.';
                showAlert(errorMsg, 'danger');
                return;
            }

            renderRouteCalculationResult(data.route);
            if (data.pricing) {
                renderPricingCalculationResult(data.pricing, data.route, data.disclaimer);
            }

        } catch (error) {
            console.error('Calculate route AJAX error:', error);
            showAlert('Terjadi gangguan jaringan atau server tidak merespon saat menghitung rute.', 'danger');
        } finally {
            state.isCalculating = false;
            calculateSpinner.classList.add('d-none');
            calculateArrowIcon.classList.remove('d-none');
            calculateBtnText.textContent = 'Hitung Ulang Rute & Biaya';
            updateUIState();
        }
    }

    function renderPricingCalculationResult(pricing, route, disclaimer) {
        if (!pricing) return;

        resPricingTotal.textContent = pricing.formatted_total || 'Rp0';
        resPackageName.textContent = pricing.rate_details ? pricing.rate_details.package_name : 'Standar';
        resBaseCost.textContent = pricing.formatted_base_cost || 'Rp0';

        const pricePerKmFormatted = pricing.rate_details ? pricing.rate_details.formatted_price_per_km : 'Rp0';
        resDistanceCalcDesc.textContent = `${route.distance_kilometers} km × ${pricePerKmFormatted}`;
        resDistanceCost.textContent = pricing.formatted_distance_cost || 'Rp0';

        const passengerCount = parseInt(passengerCountSelect.value, 10) || 1;
        const extraPassengers = Math.max(0, passengerCount - 1);
        if (extraPassengers > 0) {
            const extraPriceFormatted = pricing.rate_details ? `Rp${pricing.rate_details.extra_passenger_price.toLocaleString('id-ID')}` : '';
            resPassengerCalcDesc.textContent = `${extraPassengers} orang × ${extraPriceFormatted}`;
            resPassengerCost.textContent = pricing.formatted_passenger_cost || 'Rp0';
            resPassengerRow.classList.remove('d-none');
        } else {
            resPassengerCalcDesc.textContent = '0 orang';
            resPassengerCost.textContent = 'Rp0 (Gratis)';
        }

        const addCost = (pricing.parking_cost || 0) + (pricing.toll_cost || 0) + (pricing.night_service_cost || 0);
        resAdditionalCost.textContent = addCost > 0 ? `Rp${addCost.toLocaleString('id-ID')}` : 'Rp0';

        resSubtotalCost.textContent = `Rp${(pricing.subtotal || 0).toLocaleString('id-ID')}`;

        if (pricing.minimum_price_applied) {
            resMinimumPriceVal.textContent = `Rp${(pricing.minimum_price || 0).toLocaleString('id-ID')}`;
            resMinimumPriceNotice.classList.remove('d-none');
        } else {
            resMinimumPriceNotice.classList.add('d-none');
        }

        if (disclaimer) {
            resPricingDisclaimer.innerHTML = `<i class="bi bi-shield-check me-1"></i> ${escapeHtml(disclaimer)}`;
        }
    }

    function renderRouteCalculationResult(routeData) {
        // Tampilkan metrik ringkasan
        resTotalDistance.textContent = `${routeData.distance_kilometers} km`;
        resTotalDuration.textContent = routeData.duration_text;

        // Render Legs / Segmen
        resLegsList.innerHTML = '';
        if (Array.isArray(routeData.legs) && routeData.legs.length > 0) {
            routeData.legs.forEach((leg, index) => {
                const legEl = document.createElement('div');
                legEl.className = 'p-3 bg-light rounded-3 border-start border-3 border-primary small mb-2';
                legEl.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark">Segmen ${index + 1}</strong>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            ${leg.distance_kilometers} km &bull; ${escapeHtml(leg.duration_text)}
                        </span>
                    </div>
                    <div class="text-muted" style="font-size: 0.8rem;">
                        <span>${escapeHtml(leg.from.name)}</span>
                        <i class="bi bi-arrow-right mx-1 text-primary"></i>
                        <span class="text-dark fw-semibold">${escapeHtml(leg.to.name)}</span>
                    </div>
                `;
                resLegsList.appendChild(legEl);
            });
        }

        routeResultContainer.classList.remove('d-none');

        // Gambar Polyline jika Google Maps geometry decoding tersedia dan encoded_polyline ada
        if (state.mapReady && state.map && routeData.encoded_polyline) {
            try {
                if (window.google && google.maps && google.maps.geometry && google.maps.geometry.encoding) {
                    if (state.polyline) {
                        state.polyline.setMap(null);
                        state.polyline = null;
                    }

                    const decodedPath = google.maps.geometry.encoding.decodePath(routeData.encoded_polyline);

                    state.polyline = new google.maps.Polyline({
                        path: decodedPath,
                        geodesic: true,
                        strokeColor: '#0d9488', // brand accent
                        strokeOpacity: 0.85,
                        strokeWeight: 5
                    });

                    state.polyline.setMap(state.map);

                    const bounds = new google.maps.LatLngBounds();
                    decodedPath.forEach(pt => bounds.extend(pt));
                    state.map.fitBounds(bounds);
                }
            } catch (polyErr) {
                console.warn('Polyline render warning:', polyErr);
            }
        }
    }

    // 10. Tombol Event Listeners
    btnAddDestination.addEventListener('click', function() {
        const val = destinationSelect.value;
        if (!val) {
            showAlert('Silakan pilih salah satu tempat wisata dari daftar dropdown.', 'info');
            return;
        }
        addDestination(val);
    });

    btnClearAll.addEventListener('click', clearAllDestinations);

    btnCalculateRoute.addEventListener('click', calculateTourRoute);

    if (passengerCountSelect) {
        passengerCountSelect.addEventListener('change', function() {
            if (!routeResultContainer.classList.contains('d-none')) {
                calculateBtnText.textContent = 'Hitung Ulang Estimasi Biaya';
            }
        });
    }

    btnResetMapView.addEventListener('click', function() {
        if (state.mapReady && state.map) {
            fitMapBounds();
        }
    });

    // 11. Load Google Maps Script secara dinamis dengan libraries=geometry
    if (googleMapsBrowserKey && googleMapsBrowserKey.trim() !== '') {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(googleMapsBrowserKey)}&callback=initCalculatorMap&loading=async&libraries=geometry`;
        script.async = true;
        script.defer = true;
        script.onerror = function() {
            console.warn('Gagal memuat Google Maps SDK dari Google servers.');
            activateMapFallback();
        };
        document.head.appendChild(script);
    } else {
        activateMapFallback();
    }

    // Inisialisasi awal UI
    renderDestinationsList();
    updateUIState();

})();
</script>
@endpush
