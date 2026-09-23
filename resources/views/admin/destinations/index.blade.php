@extends('layouts.admin')

@section('title', 'Kelola Destinasi Wisata')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Daftar Tempat Wisata</h1>
            <p class="text-muted mb-0 small">Kelola objek wisata Bali, titik koordinat peta, foto utama, dan urutan katalog.</p>
        </div>
        <div>
            <a href="{{ route('admin.destinations.create') }}" class="btn btn-brand-primary d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Tempat Wisata</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.destinations.index') }}" class="row g-2 align-items-center">
                <!-- Search Input -->
                <div class="col-md-4 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="q" 
                               class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Cari nama, alamat..." 
                               value="{{ $search }}">
                    </div>
                </div>

                <!-- Region Filter -->
                <div class="col-md-3 col-lg-2">
                    <select name="region_id" class="form-select bg-light">
                        <option value="">Semua Daerah</option>
                        @foreach($regions as $r)
                            <option value="{{ $r->id }}" {{ $regionId == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="col-md-3 col-lg-2">
                    <select name="category_id" class="form-select bg-light">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2 col-lg-2">
                    <select name="status" class="form-select bg-light">
                        <option value="">Semua Status</option>
                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $status === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary px-3">
                        Filter
                    </button>
                    @if($search !== '' || !empty($regionId) || !empty($categoryId) || ($status !== null && $status !== ''))
                        <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline-secondary px-3 ms-1">
                            Reset
                        </a>
                    @endif
                </div>

                @if($search !== '' || !empty($regionId) || !empty($categoryId) || ($status !== null && $status !== ''))
                    <div class="col-12 mt-2">
                        <small class="text-muted">
                            Menampilkan hasil filter: <strong>{{ $destinations->total() }}</strong> tempat wisata ditemukan.
                        </small>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            @if($destinations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4" style="width: 50px;">#</th>
                                <th style="width: 70px;">Foto</th>
                                <th>Nama Tempat Wisata</th>
                                <th>Daerah</th>
                                <th>Kategori</th>
                                <th>Koordinat</th>
                                <th class="text-center" style="width: 80px;">Urutan</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4" style="min-width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($destinations as $index => $item)
                                <tr>
                                    <td class="ps-4 text-muted small">
                                        {{ $destinations->firstItem() + $index }}
                                    </td>
                                    <td>
                                        @if(!empty($item->image_path))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($item->image_path) }}" 
                                                 alt="{{ $item->name }}" 
                                                 class="rounded object-fit-cover shadow-sm border" 
                                                 style="width: 56px; height: 42px;">
                                        @else
                                            <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" 
                                                 style="width: 56px; height: 42px;">
                                                <i class="bi bi-image small"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <a href="{{ route('admin.destinations.show', $item) }}" class="text-dark text-decoration-none hover-primary">
                                                {{ $item->name }}
                                            </a>
                                        </div>
                                        @if($item->address)
                                            <div class="text-muted small text-truncate" style="max-width: 240px;">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $item->address }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $item->region ? $item->region->name : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->category)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                {{ $item->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-monospace small text-muted">
                                            <span>{{ number_format((float)$item->latitude, 4) }}</span>, 
                                            <span>{{ number_format((float)$item->longitude, 4) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary small">
                                            #{{ $item->display_order }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('admin.destinations.status', $item) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm p-0 border-0" 
                                                    title="Klik untuk {{ $item->is_active ? 'menonaktifkan' : 'mengaktifkan' }}"
                                                    aria-label="Ubah status publikasi {{ $item->name }}">
                                                @if($item->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                                        <i class="bi bi-dash-circle me-1"></i>Nonaktif
                                                    </span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.destinations.show', $item) }}" 
                                               class="btn btn-outline-secondary" 
                                               title="Lihat Detail"
                                               aria-label="Lihat detail destinasi {{ $item->name }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.destinations.edit', $item) }}" 
                                               class="btn btn-outline-secondary" 
                                               title="Edit Destinasi"
                                               aria-label="Edit destinasi {{ $item->name }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.destinations.destroy', $item) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi \'{{ addslashes($item->name) }}\'? Seluruh data dan gambar terkait akan dihapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="Hapus Destinasi"
                                                        aria-label="Hapus destinasi {{ $item->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($destinations->hasPages())
                    <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $destinations->firstItem() }} sampai {{ $destinations->lastItem() }} dari total {{ $destinations->total() }} data
                        </small>
                        <div>
                            {{ $destinations->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="bi bi-pin-map display-4 opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">
                        {{ $search !== '' || !empty($regionId) || !empty($categoryId) ? 'Tempat wisata tidak ditemukan' : 'Belum ada data tempat wisata' }}
                    </h5>
                    <p class="text-muted small mb-3">
                        {{ $search !== '' || !empty($regionId) || !empty($categoryId) ? 'Coba ubah filter atau kata kunci pencarian Anda.' : 'Mulai tambahkan tempat wisata di Bali untuk katalog layanan tour.' }}
                    </p>
                    @if($search !== '' || !empty($regionId) || !empty($categoryId))
                        <a href="{{ route('admin.destinations.index') }}" class="btn btn-sm btn-outline-secondary">
                            Reset Filter
                        </a>
                    @else
                        <a href="{{ route('admin.destinations.create') }}" class="btn btn-sm btn-brand-primary">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Tempat Wisata Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
