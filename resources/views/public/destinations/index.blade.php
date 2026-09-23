@extends('layouts.app')

@section('title', __('destinations.title') . ' - ' . ($brand['name'] ?? 'Bali Tour Service'))
@section('meta_description', __('destinations.subtitle'))

@section('content')
<!-- Page Header -->
<section class="bg-brand-primary text-white py-4 py-md-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">{{ __('nav.home') }}</a></li>
                <li class="breadcrumb-item active text-brand-secondary" aria-current="page">{{ __('nav.destinations') }}</li>
            </ol>
        </nav>
        <h1 class="fw-bold mb-2 display-6">{{ __('destinations.title') }}</h1>
        <p class="text-white-50 mb-0 lead fs-6">
            {{ __('destinations.subtitle') }}
        </p>
    </div>
</section>

<!-- Main Filter & Grid Content -->
<section class="py-5">
    <div class="container">
        <!-- Filter Card -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
            <form method="GET" action="{{ route('destinations.index') }}" class="row g-2 align-items-center">
                <!-- Search -->
                <div class="col-md-4 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="q" 
                               class="form-control bg-light border-start-0 ps-0" 
                               placeholder="{{ __('destinations.search_placeholder') }}" 
                               value="{{ $search }}">
                    </div>
                </div>

                <!-- Region Filter -->
                <div class="col-md-3 col-lg-3">
                    <select name="region" class="form-select bg-light">
                        <option value="">{{ __('destinations.filter_region') }}</option>
                        @foreach($regions as $r)
                            <option value="{{ $r->slug }}" {{ $regionSlug === $r->slug ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="col-md-3 col-lg-2">
                    <select name="category" class="form-select bg-light">
                        <option value="">{{ __('destinations.filter_category') }}</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->slug }}" {{ $categorySlug === $c->slug ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-2 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-brand-primary w-100">
                        {{ __('destinations.filter_button') }}
                    </button>
                    @if($search !== '' || !empty($regionSlug) || !empty($categorySlug))
                        <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary" title="{{ __('destinations.reset_search') }}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Filter Status Alert if active -->
        @if($search !== '' || !empty($regionSlug) || !empty($categorySlug))
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <span class="text-muted small">
                    {{ __('destinations.showing_results', ['count' => $destinations->total()]) }}
                </span>
                <a href="{{ route('destinations.index') }}" class="text-decoration-none small text-muted">
                    <i class="bi bi-x-circle me-1"></i>{{ __('destinations.clear_filters') }}
                </a>
            </div>
        @endif

        <!-- Destinations Grid -->
        @if($destinations->count() > 0)
            <div class="row g-4">
                @foreach($destinations as $destination)
                    <div class="col-lg-4 col-md-6">
                        <x-destination-card :destination="$destination" />
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($destinations->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $destinations->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
                <div class="icon-circle bg-light text-muted mx-auto mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-pin-map fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">
                    {{ $search !== '' || !empty($regionSlug) || !empty($categorySlug) ? __('destinations.not_found') : __('destinations.empty') }}
                </h4>
                <p class="text-muted small max-w-500 mx-auto mb-4">
                    {{ $search !== '' || !empty($regionSlug) || !empty($categorySlug) 
                        ? __('destinations.empty_desc') 
                        : __('destinations.empty_catalog') }}
                </p>
                <div>
                    @if($search !== '' || !empty($regionSlug) || !empty($categorySlug))
                        <a href="{{ route('destinations.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2">
                            {{ __('destinations.reset_search') }}
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-brand-primary btn-sm rounded-pill px-4 py-2">
                            {{ __('common.back_to_home') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
