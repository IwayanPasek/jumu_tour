@props([
    'size' => 'default', // 'small', 'default', 'large'
    'link' => true,
])

@php
    $logoClass = match($size) {
        'small' => 'brand-logo-sm',
        'large' => 'brand-logo-lg',
        default => 'brand-logo-md',
    };
    $iconSize = match($size) {
        'small' => 'fs-6',
        'large' => 'fs-3',
        default => 'fs-5',
    };
@endphp

@if ($link)
    <a href="{{ route('home') }}" class="brand-identity d-inline-flex align-items-center gap-2 text-decoration-none {{ $attributes->get('class') }}" aria-label="{{ $brand['name'] }} - Beranda">
@else
    <div class="brand-identity d-inline-flex align-items-center gap-2 {{ $attributes->get('class') }}">
@endif

    @if (!empty($brand['logo_url']))
        <img src="{{ $brand['logo_url'] }}" 
             alt="{{ $brand['name'] }}" 
             class="brand-logo-img {{ $logoClass }}"
             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
        <div class="brand-fallback-badge d-none badge rounded-circle p-2 text-dark bg-brand-secondary">
            <i class="bi bi-compass {{ $iconSize }}"></i>
        </div>
    @else
        <div class="brand-fallback-badge badge rounded-circle p-2 text-dark bg-brand-secondary shadow-sm">
            <i class="bi bi-compass {{ $iconSize }}"></i>
        </div>
    @endif

    <span class="brand-name-text fw-bold text-brand-surface tracking-tight">
        {{ $brand['name'] }}
    </span>

@if ($link)
    </a>
@else
    </div>
@endif
