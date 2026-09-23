@extends('layouts.app')

@section('title', 'Sesi Kedaluwarsa (419) - ' . ($brandSetting->brand_name ?? 'Jumu Bali Tour'))

@section('content')
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="p-4 p-md-5 rounded-4 shadow-sm bg-white border">
                <div class="text-warning mb-3">
                    <i class="bi bi-hourglass-split display-1"></i>
                </div>
                <h1 class="h2 fw-bold text-dark mb-2">Sesi Kedaluwarsa (419)</h1>
                <p class="text-muted mb-4 lead fs-6">
                    Sesi pengiriman formulir Anda telah berakhir demi alasan keamanan. Silakan muat ulang halaman untuk memperbarui token keamanan CSRF.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" onclick="window.location.reload();" class="btn btn-brand-primary px-4 py-2 rounded-pill">
                        <i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang Halaman
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                        <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
