<nav class="navbar navbar-expand-lg navbar-dark bg-brand-primary sticky-top shadow-sm py-2 py-lg-3" aria-label="Navigasi Utama">
    <div class="container">
        <!-- Brand Identity Logo / Text -->
        <x-brand-logo size="default" class="me-3" />

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 p-2" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarMainCollapse" 
                aria-controls="navbarMainCollapse" 
                aria-expanded="false" 
                aria-label="Buka navigasi menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarMainCollapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 py-2 text-white {{ request()->routeIs('home') ? 'active' : 'opacity-75' }}" 
                       aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}" 
                       href="{{ route('home') }}">
                        {{ __('nav.home') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 py-2 text-white {{ request()->routeIs('regions.*') ? 'active' : 'opacity-75' }}" 
                       aria-current="{{ request()->routeIs('regions.*') ? 'page' : 'false' }}" 
                       href="{{ route('regions.index') }}">
                        {{ __('nav.regions') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 py-2 text-white {{ request()->routeIs('categories.*') ? 'active' : 'opacity-75' }}" 
                       aria-current="{{ request()->routeIs('categories.*') ? 'page' : 'false' }}" 
                       href="{{ route('categories.index') }}">
                        {{ __('nav.categories') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 py-2 text-white {{ request()->routeIs('destinations.*') ? 'active' : 'opacity-75' }}" 
                       aria-current="{{ request()->routeIs('destinations.*') ? 'page' : 'false' }}" 
                       href="{{ route('destinations.index') }}">
                        {{ __('nav.destinations') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 py-2 text-white {{ request()->routeIs('calculator.*') ? 'active' : 'opacity-75' }}" 
                       aria-current="{{ request()->routeIs('calculator.*') ? 'page' : 'false' }}" 
                       href="{{ route('calculator.index') }}">
                        {{ __('nav.calculator') }}
                    </a>
                </li>
                
                <!-- Language Switcher Component -->
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <x-language-switcher />
                </li>

                <!-- CTA Button WhatsApp / Kontak -->
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    @if ($brand['has_valid_whatsapp'])
                        <a href="{{ $brand['whatsapp_url'] }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="btn btn-brand-secondary px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2 shadow-sm"
                           aria-label="{{ __('nav.contact') }} {{ $brand['name'] }}">
                            <i class="bi bi-whatsapp"></i>
                            <span>{{ __('nav.contact') }}</span>
                        </a>
                    @else
                        <button type="button" 
                                class="btn btn-outline-light px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2 small opacity-75" 
                                data-bs-toggle="modal" 
                                data-bs-target="#contactInfoModal"
                                aria-label="{{ __('nav.contact') }}">
                            <i class="bi bi-chat-dots"></i>
                            <span>{{ __('nav.contact') }}</span>
                        </button>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal Info Kontak jika WhatsApp Belum Dikonfigurasi -->
<div class="modal fade" id="contactInfoModal" tabindex="-1" aria-labelledby="contactInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-brand-primary text-white">
                <h5 class="modal-title fw-bold" id="contactInfoModalLabel">Informasi Kontak {{ $brand['name'] }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-3">{{ $brand['about'] }}</p>
                <div class="alert alert-info d-flex align-items-center mb-0 small" role="alert">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        Layanan WhatsApp resmi sedang dalam tahap persiapan akhir untuk pengujian MVP.
                        @if (!empty($brand['email']))
                            Silakan hubungi kami via email: <strong>{{ $brand['email'] }}</strong>.
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
