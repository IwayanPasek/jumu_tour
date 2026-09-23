<footer class="bg-brand-primary text-white pt-5 pb-4 mt-auto border-top border-secondary border-opacity-25">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- Brand Overview Column -->
            <div class="col-lg-5 col-md-6">
                <div class="mb-3">
                    <x-brand-logo size="default" :link="false" />
                </div>
                <p class="text-white-50 small mb-3 lh-base">
                    {{ $brand['tagline'] }}. {{ $brand['about'] }}
                </p>

                @if ($brand['has_valid_whatsapp'])
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <a href="{{ $brand['whatsapp_url'] }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="btn btn-brand-secondary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2"
                           aria-label="Konsultasi via WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                            <span>Chat WhatsApp</span>
                        </a>
                    </div>
                @else
                    <div class="badge bg-secondary text-white-50 small py-2 px-3 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> WhatsApp dalam konfigurasi
                    </div>
                @endif
            </div>

            <!-- Navigation Links Column -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-brand-secondary mb-3 text-uppercase small tracking-wide">Navigasi</h6>
                <ul class="list-unstyled text-white-50 small d-flex flex-column gap-2 mb-0">
                    <li>
                        <a href="{{ route('home') }}" class="text-white-50 text-decoration-none hover-text-white">
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('regions.index') }}" class="text-white-50 text-decoration-none hover-text-white">
                            Daftar Daerah
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('categories.index') }}" class="text-white-50 text-decoration-none hover-text-white">
                            Kategori Wisata
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('calculator.index') }}" class="text-white-50 text-decoration-none hover-text-white">
                            Kalkulator Rute & Tarif
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact & Service Information Column -->
            <div class="col-lg-4 col-md-12">
                <h6 class="fw-bold text-brand-secondary mb-3 text-uppercase small tracking-wide">Informasi Layanan</h6>
                <p class="text-white-50 small mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-brand-secondary"></i>
                    <span>Bali, Indonesia</span>
                </p>
                @if (!empty($brand['email']))
                    <p class="text-white-50 small mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-fill text-brand-secondary"></i>
                        <span>{{ $brand['email'] }}</span>
                    </p>
                @endif
                <p class="text-white-50 small mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-fill text-brand-secondary"></i>
                    <span>Jam Operasional: 07.00 - 22.00 WITA</span>
                </p>
            </div>
        </div>

        <hr class="my-4 border-secondary opacity-25">

        <!-- Copyright Row -->
        <div class="row align-items-center g-2">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-white-50 small mb-0">
                    &copy; {{ date('Y') }} <strong class="text-white">{{ $brand['name'] }}</strong>. Seluruh hak cipta dilindungi.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 px-2 py-1 small">
                    Platform Tour Bali MVP
                </span>
            </div>
        </div>
    </div>
</footer>
