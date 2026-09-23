@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Daftar Kategori Wisata</h1>
            <p class="text-muted mb-0 small">Kelola klasifikasi tema, jenis wisata, dan status publikasi.</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-brand-primary d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Kategori Baru</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="q" 
                               class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Cari nama kategori, deskripsi..." 
                               value="{{ $search }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary px-3">
                        Cari
                    </button>
                    @if($search !== '')
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-3 ms-1">
                            Reset
                        </a>
                    @endif
                </div>
                @if($search !== '')
                    <div class="col-12 mt-2">
                        <small class="text-muted">
                            Menampilkan hasil pencarian untuk: <strong>"{{ $search }}"</strong> ({{ $categories->total() }} data)
                        </small>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4" style="width: 50px;">#</th>
                                <th>Nama Kategori</th>
                                <th>Slug URL</th>
                                <th class="text-center">Destinasi</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4" style="min-width: 170px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $category)
                                <tr>
                                    <td class="ps-4 text-muted small">
                                        {{ $categories->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $category->name }}</div>
                                        @if($category->description)
                                            <div class="text-muted small text-truncate" style="max-width: 320px;">
                                                {{ Str::limit($category->description, 60) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="text-primary small">{{ $category->slug }}</code>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $category->destinations_count > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }} rounded-pill px-2 py-1">
                                            <i class="bi bi-pin-map me-1"></i>{{ $category->destinations_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('admin.categories.status', $category) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm p-0 border-0" 
                                                    title="Klik untuk {{ $category->is_active ? 'menonaktifkan' : 'mengaktifkan' }}"
                                                    aria-label="Ubah status kategori {{ $category->name }}">
                                                @if($category->is_active)
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
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="btn btn-outline-secondary" 
                                               title="Edit Kategori"
                                               aria-label="Edit kategori {{ $category->name }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            @if($category->destinations_count > 0)
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Tidak dapat dihapus karena digunakan oleh {{ $category->destinations_count }} destinasi wisata" 
                                                        aria-label="Hapus kategori {{ $category->name }} tidak dapat dilakukan"
                                                        onclick="alert('Kategori \'{{ addslashes($category->name) }}\' tidak dapat dihapus karena masih digunakan oleh {{ $category->destinations_count }} destinasi wisata terkait. Silakan nonaktifkan kategori ini.');">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <form method="POST" 
                                                      action="{{ route('admin.categories.destroy', $category) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ addslashes($category->name) }}\'?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger" 
                                                            title="Hapus Kategori"
                                                            aria-label="Hapus kategori {{ $category->name }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($categories->hasPages())
                    <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $categories->firstItem() }} sampai {{ $categories->lastItem() }} dari total {{ $categories->total() }} data
                        </small>
                        <div>
                            {{ $categories->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="bi bi-tags display-4 opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">
                        {{ $search !== '' ? 'Kategori tidak ditemukan' : 'Belum ada data kategori' }}
                    </h5>
                    <p class="text-muted small mb-3">
                        {{ $search !== '' ? 'Coba ubah kata kunci pencarian Anda.' : 'Mulai tambahkan kategori wisata untuk mengelompokkan tempat wisata di Bali.' }}
                    </p>
                    @if($search !== '')
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
                            Reset Pencarian
                        </a>
                    @else
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-brand-primary">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Kategori Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
