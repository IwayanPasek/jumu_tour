@props([
    'destination',
    'showRegion' => true,
    'showCategory' => true,
])

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

<div class="card h-100 destination-card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <!-- Image Thumbnail or Local Placeholder -->
    <div class="destination-card-media position-relative">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" 
                 alt="{{ $destination->name }}" 
                 class="w-100 object-fit-cover" 
                 style="height: 200px;"
                 loading="lazy">
        @else
            <div class="destination-card-placeholder d-flex flex-column align-items-center justify-content-center text-white" 
                 style="height: 200px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                <i class="bi bi-geo-alt-fill fs-1 mb-1 text-brand-secondary opacity-75"></i>
                <span class="small text-white-50">{{ __('destinations.no_photo') }}</span>
            </div>
        @endif

        <!-- Floating Badges -->
        <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1">
            @if ($showCategory && $destination->category)
                <a href="{{ route('categories.show', $destination->category->slug) }}" 
                   class="badge bg-brand-primary text-white text-decoration-none shadow-sm py-1 px-2">
                    <i class="bi bi-tag-fill me-1 small text-brand-secondary"></i>
                    {{ $destination->category->name }}
                </a>
            @endif

            @if ($showRegion && $destination->region)
                <a href="{{ route('regions.show', $destination->region->slug) }}" 
                   class="badge bg-white text-dark text-decoration-none shadow-sm py-1 px-2 border">
                    <i class="bi bi-pin-map-fill me-1 small text-danger"></i>
                    {{ $destination->region->name }}
                </a>
            @endif
        </div>
    </div>

    <!-- Content Body -->
    <div class="card-body d-flex flex-column p-4">
        <h5 class="card-title fw-bold mb-2">
            <a href="{{ route('destinations.show', $destination->slug) }}" 
               class="text-dark text-decoration-none hover-primary">
                {{ $destination->name }}
            </a>
        </h5>

        <p class="card-text text-muted small mb-3 flex-grow-1 lh-base">
            {{ \Illuminate\Support\Str::limit($destination->description, 110, '...') }}
        </p>

        @if (!empty($destination->address))
            <p class="text-muted small mb-3 d-flex align-items-start gap-1">
                <i class="bi bi-geo-alt text-brand-secondary mt-1"></i>
                <span class="text-truncate">{{ $destination->address }}</span>
            </p>
        @endif

        <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto">
            <a href="{{ route('destinations.show', $destination->slug) }}" 
               class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                {{ __('destinations.view_detail') }} <i class="bi bi-arrow-right ms-1"></i>
            </a>
            <span class="text-muted small">
                {{ __('destinations.display_order', ['order' => $destination->display_order]) }}
            </span>
        </div>
    </div>
</div>
