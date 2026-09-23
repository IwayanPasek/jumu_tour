@extends('layouts.app')

@section('title', 'Daftar Daerah Wisata Bali - ' . e($brand['name']))
@section('meta_description', 'Eksplorasi wilayah dan kabupaten terpopuler di Bali untuk merencanakan rute perjalanan liburan Anda.')

@section('content')
<!-- Page Header -->
<section class="bg-brand-primary text-white py-4 py-md-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active text-brand-secondary" aria-current="page">Daerah Wisata</li>
            </ol>
        </nav>
        <h1 class="fw-bold mb-2 display-6">Daerah & Kabupaten Wisata di Bali</h1>
        <p class="text-white-50 mb-0 lead fs-6">
            Pilih wilayah tujuan Anda dari selatan pesisir pantai hingga pegunungan sejuk di Bali.
        </p>
    </div>
</section>

<!-- Main List Content -->
<section class="py-5">
    <div class="container">
        @if ($regions->count() > 0)
            <div class="row g-4">
                @foreach ($regions as $region)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 feature-box p-4 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="icon-circle bg-brand-primary bg-opacity-10 text-brand-primary">
                                    <i class="bi bi-geo-alt-fill fs-4 text-brand-secondary"></i>
                                </div>
                                <span class="badge bg-brand-secondary text-dark rounded-pill px-3 py-2 small fw-semibold">
                                    {{ $region->destinations_count }} Destinasi
                                </span>
                            </div>

                            <h4 class="fw-bold text-dark mb-1">
                                {{ $region->name }}
                            </h4>

                            @if (!empty($region->regency))
                                <span class="text-muted small fw-medium mb-2 d-block">
                                    <i class="bi bi-building me-1"></i>{{ $region->regency }}
                                </span>
                            @endif

                            <p class="text-muted small mb-4 flex-grow-1 lh-base">
                                {{ \Illuminate\Support\Str::limit($region->description, 130, '...') }}
                            </p>

                            <a href="{{ route('regions.show', $region->slug) }}" 
                               class="btn btn-outline-dark btn-sm rounded-pill py-2 px-3 fw-semibold d-inline-flex align-items-center justify-content-between mt-auto">
                                <span>Lihat Destinasi Wisata</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($regions->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $regions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
                <div class="icon-circle bg-light text-muted mx-auto mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-geo-alt fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Belum Ada Daerah yang Ditampilkan</h4>
                <p class="text-muted small max-w-500 mx-auto mb-4">
                    Data wilayah wisata sedang diperbarui oleh pengelola. Silakan kembali lagi nanti atau hubungi kami untuk informasi rute.
                </p>
                <div>
                    <a href="{{ route('home') }}" class="btn btn-brand-primary btn-sm rounded-pill px-4 py-2">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
