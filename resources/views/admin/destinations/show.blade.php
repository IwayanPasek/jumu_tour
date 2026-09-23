@extends('layouts.admin')

@section('title', 'Detail Tempat Wisata: ' . $destination->name)

@section('content')
<div class="container-fluid px-0" style="max-width: 1000px;">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline-secondary btn-sm mb-2 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>
            <h1 class="h3 fw-bold text-dark mb-1">{{ $destination->name }}</h1>
            <p class="text-muted mb-0 small">Detail data tempat wisata, foto utama, dan titik koordinat peta.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('destinations.show', $destination->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat Publik</span>
            </a>
            <a href="{{ route('admin.destinations.edit', $destination) }}" class="btn btn-brand-primary btn-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-pencil"></i>
                <span>Edit Destinasi</span>
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row g-4">
        <!-- Left: Image & Quick Info -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                @if(!empty($destination->image_path))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($destination->image_path) }}" 
                         alt="{{ $destination->name }}" 
                         class="w-100 object-fit-cover" 
                         style="height: 260px;">
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center text-muted bg-light" 
                         style="height: 260px;">
                        <i class="bi bi-image display-4 opacity-50 mb-2"></i>
                        <span class="small">Belum Ada Foto Utama</span>
                    </div>
                @endif

                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small">Status Publikasi:</span>
                        @if($destination->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-check-circle me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                <i class="bi bi-dash-circle me-1"></i>Nonaktif
                            </span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small">Urutan Tampil:</span>
                        <span class="fw-bold text-dark">#{{ $destination->display_order }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small">Dibuat Pada:</span>
                        <span class="small text-muted">{{ $destination->created_at ? $destination->created_at->translatedFormat('d M Y H:i') : '-' }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">Terakhir Diperbarui:</span>
                        <span class="small text-muted">{{ $destination->updated_at ? $destination->updated_at->translatedFormat('d M Y H:i') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Information & Coordinates -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold text-dark mb-3">Informasi Destinasi</h5>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="text-muted small d-block mb-1">Daerah Wisata (Region)</label>
                        <span class="badge bg-light text-dark border fs-6 px-3 py-2">
                            <i class="bi bi-pin-map-fill text-danger me-1"></i>
                            {{ $destination->region ? $destination->region->name : '-' }}
                        </span>
                    </div>

                    <div class="col-sm-6">
                        <label class="text-muted small d-block mb-1">Kategori Wisata (Category)</label>
                        @if($destination->category)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-2">
                                <i class="bi bi-tag-fill me-1"></i>
                                {{ $destination->category->name }}
                            </span>
                        @else
                            <span class="text-muted small">Umum / Tanpa Kategori</span>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="text-muted small d-block mb-1">Slug URL Publik</label>
                        <code class="text-primary fs-6">{{ $destination->slug }}</code>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small d-block mb-1">Alamat Lokasi</label>
                        <p class="text-dark mb-0 small">
                            {{ $destination->address ?: 'Alamat spesifik belum ditambahkan.' }}
                        </p>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small d-block mb-1">Titik Koordinat (Peta)</label>
                        <div class="p-3 bg-light rounded border font-monospace small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Latitude:</span>
                                <strong class="text-dark">{{ $destination->latitude }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Longitude:</span>
                                <strong class="text-dark">{{ $destination->longitude }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small d-block mb-1">Deskripsi Objek Wisata</label>
                        <div class="text-dark small lh-base" style="white-space: pre-line;">
                            {{ $destination->description ?: 'Belum ada deskripsi untuk tempat wisata ini.' }}
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Action Buttons -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.destinations.status', $destination) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i>
                            {{ $destination->is_active ? 'Nonaktifkan Destinasi' : 'Aktifkan Destinasi' }}
                        </button>
                    </form>

                    <form method="POST" 
                          action="{{ route('admin.destinations.destroy', $destination) }}" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi wisata ini secara permanen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Hapus Destinasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
