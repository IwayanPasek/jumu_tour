@extends('layouts.app')

@section('title', e($category->name) . ' - ' . __('categories.title'))
@section('meta_description', Str::limit(strip_tags($category->description ?? 'List of destinations in ' . $category->name . ' Bali.'), 150))

@section('content')
<!-- Header Detail Kategori -->
<section class="bg-brand-primary text-white py-4 py-md-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">{{ __('nav.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-white-50 text-decoration-none">{{ __('nav.categories') }}</a></li>
                <li class="breadcrumb-item active text-brand-secondary" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="fw-bold mb-1 display-6">{{ $category->name }}</h1>
                @if (!empty($category->description))
                    <p class="text-white-50 mb-0 lead fs-6 max-w-700">
                        {{ $category->description }}
                    </p>
                @endif
            </div>
            <div>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    <span>{{ __('categories.all_categories') }}</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Destination Cards Grid -->
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold text-dark mb-0">{{ __('categories.destinations_in_category', ['category' => $category->name]) }}</h4>
            <span class="text-muted small">{{ __('categories.showing_count', ['count' => $destinations->total()]) }}</span>
        </div>

        @if ($destinations->count() > 0)
            <div class="row g-4">
                @foreach ($destinations as $destination)
                    <div class="col-md-6 col-lg-4">
                        <x-destination-card :destination="$destination" :showRegion="true" :showCategory="false" />
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($destinations->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $destinations->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
                <div class="icon-circle bg-light text-muted mx-auto mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-tag fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ __('categories.empty') }}</h4>
                <p class="text-muted small max-w-500 mx-auto mb-4">
                    {{ __('categories.empty_desc') }}
                </p>
                <div>
                    <a href="{{ route('categories.index') }}" class="btn btn-brand-primary btn-sm rounded-pill px-4 py-2">
                        {{ __('categories.choose_other') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
