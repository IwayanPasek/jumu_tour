@extends('layouts.app')

@section('title', $destination->name . ' - ' . __('destinations.title'))
@section('meta_description', Str::limit(strip_tags($destination->description ?? 'Explore ' . $destination->name . ' in Bali with our private tour service.'), 150))

@section('content')
@php
    $imageUrl = null;
    if (!empty($destination->image_path)) {
        if (\Illuminate\Support\Str::startsWith($destination->image_path, ['http://', 'https://'])) {
            $imageUrl = $destination->image_path;
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($destination->image_path)) {
            $imageUrl = \Illuminate\Support\Facades\Storage::url($destination->image_path);
        } elseif (file_exists(public_path($destination->image_path))) {
            $imageUrl = asset($destination->image_path);
        } else {
            $imageUrl = \Illuminate\Support\Facades\Storage::url($destination->image_path);
        }
    }
@endphp

<!-- Breadcrumb Header -->
<section class="bg-brand-primary text-white py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">{{ __('nav.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('destinations.index') }}" class="text-white-50 text-decoration-none">{{ __('nav.destinations') }}</a></li>
                @if($destination->region)
                    <li class="breadcrumb-item"><a href="{{ route('regions.show', $destination->region->slug) }}" class="text-white-50 text-decoration-none">{{ $destination->region->name }}</a></li>
                @endif
                <li class="breadcrumb-item active text-brand-secondary" aria-current="page">{{ $destination->name }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Main Detail Content -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Column: Image & Details -->
            <div class="col-lg-8">
                <!-- Hero Image or Visual Placeholder -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $destination->name }}" 
                             class="w-100 object-fit-cover" 
                             style="max-height: 460px;">
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center text-white p-5" 
                             style="height: 320px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                            <i class="bi bi-geo-alt-fill display-3 mb-2 text-brand-secondary opacity-75"></i>
                            <span class="fs-5 text-white-50">{{ __('destinations.no_photo') }}</span>
                        </div>
                    @endif

                    <div class="card-body p-4 p-md-5">
                        <!-- Badges -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @if($destination->category)
                                <a href="{{ route('categories.show', $destination->category->slug) }}" 
                                   class="badge bg-brand-primary text-white text-decoration-none py-2 px-3">
                                    <i class="bi bi-tag-fill me-1 text-brand-secondary"></i>
                                    {{ $destination->category->name }}
                                </a>
                            @endif

                            @if($destination->region)
                                <a href="{{ route('regions.show', $destination->region->slug) }}" 
                                   class="badge bg-light text-dark border text-decoration-none py-2 px-3">
                                    <i class="bi bi-pin-map-fill text-danger me-1"></i>
                                    {{ $destination->region->name }}
                                </a>
                            @endif
                        </div>

                        <!-- Title -->
                        <h1 class="fw-bold text-dark display-6 mb-3">
                            {{ $destination->name }}
                        </h1>

                        <!-- Address -->
                        @if(!empty($destination->address))
                            <p class="text-muted d-flex align-items-start gap-2 mb-4">
                                <i class="bi bi-geo-alt-fill text-brand-secondary fs-5 mt-n1"></i>
                                <span>{{ $destination->address }}</span>
                            </p>
                        @endif

                        <hr class="my-4">

                        <!-- Description -->
                        <h5 class="fw-bold text-dark mb-3">{{ __('destinations.about_title') }}</h5>
                        <div class="text-secondary lh-lg mb-4" style="white-space: pre-line;">
                            {{ $destination->description ?: __('destinations.no_description') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Information -->
            <div class="col-lg-4">
                <!-- Location & Coordinates Card -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3">{{ __('destinations.location_info') }}</h5>

                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-map text-primary fs-5"></i>
                            <div>
                                <strong class="d-block text-dark">{{ __('destinations.region_area') }}</strong>
                                <span class="text-muted">{{ $destination->region ? $destination->region->name : '-' }}</span>
                            </div>
                        </li>

                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-compass text-danger fs-5"></i>
                            <div>
                                <strong class="d-block text-dark">{{ __('destinations.coordinates') }}</strong>
                                <div class="font-monospace text-muted mt-1">
                                    <span>Lat: {{ $destination->latitude }}</span><br>
                                    <span>Lng: {{ $destination->longitude }}</span>
                                </div>
                            </div>
                        </li>

                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-car-front text-success fs-5"></i>
                            <div>
                                <strong class="d-block text-dark">{{ __('destinations.transport_service') }}</strong>
                                <span class="text-muted">{{ __('destinations.transport_desc') }}</span>
                            </div>
                        </li>
                    </ul>

                    <hr class="my-3">

                    <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-pill py-2">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('destinations.back_to_catalog') }}
                    </a>
                </div>

                <!-- Related Destinations Nearby -->
                @if($nearbyDestinations->count() > 0)
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h6 class="fw-bold text-dark mb-3">
                            {{ __('destinations.nearby_recommendations', ['region' => $destination->region ? $destination->region->name : 'Bali']) }}
                        </h6>
                        <div class="d-flex flex-column gap-3">
                            @foreach($nearbyDestinations as $nearby)
                                <a href="{{ route('destinations.show', $nearby->slug) }}" class="text-decoration-none group-item d-flex align-items-center gap-3">
                                    @if(!empty($nearby->image_path))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($nearby->image_path) }}" 
                                             alt="{{ $nearby->name }}" 
                                             class="rounded-3 object-fit-cover shadow-sm border" 
                                             style="width: 60px; height: 60px; flex-shrink: 0;">
                                    @else
                                        <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted" 
                                             style="width: 60px; height: 60px; flex-shrink: 0;">
                                            <i class="bi bi-geo-alt"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="text-dark fw-semibold mb-1 small hover-primary">{{ $nearby->name }}</h6>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            {{ $nearby->category ? $nearby->category->name : ($nearby->region ? $nearby->region->name : '') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
