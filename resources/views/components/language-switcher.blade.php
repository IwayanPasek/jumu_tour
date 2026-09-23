@php
    $currentLocale = app()->getLocale();
    $locales = [
        'en' => ['name' => 'English', 'short' => 'EN'],
        'id' => ['name' => 'Bahasa Indonesia', 'short' => 'ID'],
    ];
@endphp

<div class="dropdown language-switcher d-inline-block">
    <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1" 
            type="button" 
            id="languageSwitcherDropdown" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            aria-label="{{ __('nav.language') }}: {{ $locales[$currentLocale]['name'] ?? 'English' }}">
        <i class="bi bi-globe2 text-brand-primary"></i>
        <span class="fw-semibold">{{ $locales[$currentLocale]['short'] ?? 'EN' }}</span>
        <span class="d-none d-md-inline small text-muted">({{ $locales[$currentLocale]['name'] ?? 'English' }})</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1 py-1" aria-labelledby="languageSwitcherDropdown">
        @foreach($locales as $code => $info)
            <li>
                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ $currentLocale === $code ? 'active fw-bold' : '' }}" 
                   href="{{ route('language.switch', $code) }}"
                   aria-current="{{ $currentLocale === $code ? 'true' : 'false' }}">
                    <span>{{ $info['name'] }}</span>
                    @if($currentLocale === $code)
                        <i class="bi bi-check2 ms-2 text-primary"></i>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
