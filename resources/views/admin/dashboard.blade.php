@extends('layouts.admin')

@section('title', __('admin.dashboard'))

@section('content')
<div class="container-fluid p-0">
    <!-- Welcome Banner -->
    <div class="card border-0 shadow-sm rounded-4 bg-brand-primary text-white p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 text-brand-secondary small mb-2">
                    <i class="bi bi-shield-check"></i>
                    <span>{{ __('admin.active_session') }}</span>
                </div>
                <h3 class="fw-bold mb-1">{{ __('admin.welcome', ['name' => Auth::user()->name]) }}</h3>
                <p class="text-white-50 mb-0 small">
                    {{ __('admin.welcome_desc', ['name' => $brand['name'] ?? 'Bali Tour Service']) }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-brand-secondary text-dark p-2 px-3 rounded-pill fw-semibold">
                    <i class="bi bi-clock me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Daerah -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">{{ __('admin.total_regions') }}</span>
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary" style="width: 42px; height: 42px;">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $stats['total_regions'] }}</h3>
                <span class="text-muted small">{{ __('admin.regencies_cities') }}</span>
            </div>
        </div>

        <!-- Kategori -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">{{ __('admin.total_categories') }}</span>
                    <div class="icon-circle bg-warning bg-opacity-15 text-warning" style="width: 42px; height: 42px;">
                        <i class="bi bi-tags-fill fs-5 text-dark"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $stats['total_categories'] }}</h3>
                <span class="text-muted small">{{ __('admin.holiday_themes') }}</span>
            </div>
        </div>

        <!-- Destinasi -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">{{ __('admin.total_destinations') }}</span>
                    <div class="icon-circle bg-success bg-opacity-10 text-success" style="width: 42px; height: 42px;">
                        <i class="bi bi-pin-map-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $stats['total_destinations'] }}</h3>
                <span class="text-muted small">{{ __('admin.registered_attractions') }}</span>
            </div>
        </div>

        <!-- Konfigurasi Tarif -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">{{ __('admin.pricing_status') }}</span>
                    <div class="icon-circle bg-info bg-opacity-10 text-info" style="width: 42px; height: 42px;">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </div>
                </div>
                <h6 class="fw-bold text-dark mb-1 text-truncate">
                    {{ $stats['active_pricing']?->name ?? __('admin.not_configured') }}
                </h6>
                <span class="badge bg-success-subtle text-success small">
                    {{ __('admin.active_rate') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Cluster Information Box -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h5 class="fw-bold text-dark mb-2">{{ __('admin.foundation_ready') }}</h5>
        <p class="text-muted small mb-3">
            {{ __('admin.foundation_desc') }}
        </p>
        <div class="alert alert-light border d-flex align-items-center mb-0 small text-muted">
            <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
            <div>
                {{ __('admin.security_note') }}
            </div>
        </div>
    </div>
</div>
@endsection
