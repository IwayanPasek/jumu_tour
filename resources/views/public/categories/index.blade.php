@extends('layouts.app')

@section('title', 'Kategori Wisata Bali - ' . e($brand['name']))
@section('meta_description', 'Jelajahi berbagai jenis pengalaman wisata di Bali mulai dari pantai, pura, alam pegunungan, hingga budaya.')

@section('content')
<!-- Page Header -->
<section class="bg-brand-primary text-white py-4 py-md-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active text-brand-secondary" aria-current="page">Kategori Wisata</li>
            </ol>
        </nav>
        <h1 class="fw-bold mb-2 display-6">Kategori Wisata di Bali</h1>
        <p class="text-white-50 mb-0 lead fs-6">
            Temukan tempat wisata impian berdasarkan tema pengalaman liburan favorit Anda.
        </p>
    </div>
</section>

<!-- Main List Content -->
<section class="py-5">
    <div class="container">
        @if ($categories->count() > 0)
            <div class="row g-4">
                @foreach ($categories as $category)
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm rounded-4 feature-box p-4 d-flex flex-column text-center">
                            <div class="icon-circle bg-brand-primary bg-opacity-10 text-brand-secondary mx-auto mb-3">
                                <i class="bi bi-tag-fill fs-4"></i>
                            </div>

                            <h5 class="fw-bold text-dark mb-1">
                                {{ $category->name }}
                            </h5>

                            <span class="badge bg-secondary-subtle text-secondary small rounded-pill px-3 py-1 mb-3 mx-auto">
                                {{ $category->destinations_count }} Destinasi
                            </span>

                            @if (!empty($category->description))
                                <p class="text-muted small mb-4 flex-grow-1 lh-base">
                                    {{ \Illuminate\Support\Str::limit($category->description, 100, '...') }}
                                </p>
                            @else
                                <div class="flex-grow-1"></div>
                            @endif

                            <a href="{{ route('categories.show', $category->slug) }}" 
                               class="btn btn-outline-dark btn-sm rounded-pill py-2 px-3 fw-semibold mt-auto">
                                <span>Lihat Wisata</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($categories->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
                <div class="icon-circle bg-light text-muted mx-auto mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-tags fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Belum Ada Kategori Wisata</h4>
                <p class="text-muted small max-w-500 mx-auto mb-4">
                    Kategori wisata sedang disusun. Silakan kembali beberapa saat lagi.
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
