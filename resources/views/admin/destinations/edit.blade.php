@extends('layouts.admin')

@section('title', 'Edit Tempat Wisata: ' . $destination->name)

@section('content')
<div class="container-fluid px-0" style="max-width: 960px;">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline-secondary btn-sm mb-2 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>
            <h1 class="h3 fw-bold text-dark mb-1">Edit Tempat Wisata: {{ $destination->name }}</h1>
            <p class="text-muted mb-0 small">Perbarui data informasi, koordinat, foto, atau status publikasi tempat wisata.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.destinations.update', $destination) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                @include('admin.destinations._form')

                <hr class="my-4">

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a href="{{ route('admin.destinations.index') }}" class="btn btn-light px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-brand-primary px-4">
                        <i class="bi bi-save me-1"></i>Perbarui Destinasi
                    </button>
                </div>
            </form>

            @if(!empty($destination->image_path))
                <form id="delete-destination-image-form" 
                      method="POST" 
                      action="{{ route('admin.destinations.image.destroy', $destination) }}" 
                      class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
