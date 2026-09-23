@extends('layouts.app')

@section('title', e($brand['name']) . ' - ' . e($brand['tagline']))

@section('content')
<!-- Hero Section -->
<section class="hero-wrapper text-white py-5 py-lg-6 position-relative">
    <div class="hero-glow"></div>
    <div class="container position-relative py-4 py-lg-5">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-7 text-center text-lg-start">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 text-brand-secondary small mb-3">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Layanan Private & Custom Tour Bali</span>
                </div>

                <h1 class="display-4 fw-bold lh-sm mb-3">
                    Jelajahi Pesona Bali Bersama <span class="text-brand-secondary">{{ $brand['name'] }}</span>
                </h1>

                <p class="lead text-white-50 mb-4 pe-lg-3 fs-6 fs-md-5">
                    {{ $brand['tagline'] }}. Nikmati kebebasan menentukan rute wisata sendiri dengan transparansi estimasi jarak dan biaya tanpa repot.
                </p>

                <!-- CTA Group -->
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                    <a href="{{ route('regions.index') }}" class="btn btn-brand-secondary btn-lg px-4 py-3 rounded-pill d-inline-flex align-items-center justify-content-center gap-2 shadow">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Jelajahi Daerah Wisata</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-tags-fill"></i>
                        <span>Lihat Kategori Wisata</span>
                    </a>
                </div>

                <!-- WhatsApp Direct CTA if Available -->
                @if ($brand['has_valid_whatsapp'])
                    <div class="mt-4 pt-2">
                        <a href="{{ $brand['whatsapp_url'] }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="text-decoration-none text-white-50 small hover-text-white d-inline-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp text-success fs-5"></i>
                            <span>Konsultasi instan dengan customer care via WhatsApp</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Hero Highlight Box (Statis) -->
            <div class="col-lg-5">
                <div class="card bg-white bg-opacity-10 border border-white border-opacity-20 text-white rounded-4 p-4 shadow-lg backdrop-blur">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="icon-circle bg-brand-secondary text-dark">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Perjalanan Terencana</h5>
                            <small class="text-white-50">Transparan & Bebas Kendala</small>
                        </div>
                    </div>
                    <p class="text-white-50 small mb-3 lh-base">
                        Sistem kami dirancang untuk memberikan estimasi rute dan biaya perjalanan yang jelas sebelum Anda melakukan pemesanan.
                    </p>
                    <ul class="list-unstyled d-flex flex-column gap-2 small text-white-50 mb-0">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-brand-secondary fs-5"></i>
                            <span>Tentukan lokasi jemput sesuai akomodasi</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-brand-secondary fs-5"></i>
                            <span>Pilih beberapa destinasi sekaligus dalam 1 hari</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-brand-secondary fs-5"></i>
                            <span>Estimasi durasi & jarak berbasis peta digital</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Keunggulan Layanan -->
<section class="py-5 bg-white border-bottom">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge-soft-warning px-3 py-1 rounded-pill small text-uppercase mb-2 d-inline-block">
                Nilai Utama
            </span>
            <h2 class="fw-bold mt-1">Mengapa Merencanakan Liburan Bersama Kami?</h2>
            <p class="text-muted small">Dedikasi kami untuk memberikan standar kenyamanan dan fleksibilitas perjalanan di Bali.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box p-4 h-100 shadow-sm">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-car-front-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Armada Nyaman & Terawat</h5>
                    <p class="text-muted small mb-0 lh-base">
                        Kendaraan bersih, kabin sejuk, dan terawat secara berkala demi menjamin kenyamanan Anda sepanjang perjalanan di Bali.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box p-4 h-100 shadow-sm">
                    <div class="icon-circle bg-warning bg-opacity-15 text-dark mb-3">
                        <i class="bi bi-signpost-split-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Rute Fleksibel & Terarah</h5>
                    <p class="text-muted small mb-0 lh-base">
                        Bebas mengatur tempat kunjungan tanpa harus terburu-buru mengikuti jadwal rombongan wisata lain.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box p-4 h-100 shadow-sm">
                    <div class="icon-circle bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Estimasi Tarif Transparan</h5>
                    <p class="text-muted small mb-0 lh-base">
                        Sistem menghitung perkiraan biaya sewa berdasarkan jarak tempuh dan rute sebelum Anda mengajukan pemesanan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Pratinjau Kategori & Ajakan Eksplorasi -->
<section id="katalog-info" class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge-soft-secondary px-3 py-1 rounded-pill small text-uppercase mb-2 d-inline-block">
                Pilihan Wisata
            </span>
            <h2 class="fw-bold mt-1">Eksplorasi Daerah & Kategori Wisata</h2>
            <p class="text-muted small">
                Temukan inspirasi destinasi berdasarkan daerah tujuan atau kategori wisata favorit Anda.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Box Daerah -->
            <div class="col-md-6 col-lg-5">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white d-flex flex-column text-center">
                    <div class="icon-circle bg-brand-primary bg-opacity-10 text-brand-primary mx-auto mb-3">
                        <i class="bi bi-pin-map-fill fs-3 text-brand-secondary"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Daftar Daerah Wisata</h4>
                    <p class="text-muted small mb-4 flex-grow-1 lh-base">
                        Jelajahi destinasi yang dikelompokkan berdasarkan wilayah kabupaten seperti Badung, Gianyar (Ubud), Tabanan, Bangli, dan lainnya.
                    </p>
                    <a href="{{ route('regions.index') }}" class="btn btn-brand-primary btn-sm rounded-pill px-4 py-2 fw-semibold">
                        Lihat Semua Daerah
                    </a>
                </div>
            </div>

            <!-- Box Kategori -->
            <div class="col-md-6 col-lg-5">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white d-flex flex-column text-center">
                    <div class="icon-circle bg-brand-secondary bg-opacity-20 text-dark mx-auto mb-3">
                        <i class="bi bi-tag-fill fs-3 text-brand-secondary"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Kategori Pengalaman</h4>
                    <p class="text-muted small mb-4 flex-grow-1 lh-base">
                        Pilih suasana liburan yang Anda inginkan dari wisata pantai, pura bersejarah, alam persawahan, hingga air terjun tersembunyi.
                    </p>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-4 py-2 fw-semibold">
                        Lihat Semua Kategori
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Placeholder Kalkulator Info -->
<section id="kalkulator-info" class="py-5 bg-white border-top">
    <div class="container py-4">
        <div class="card bg-brand-primary text-white rounded-4 p-4 p-md-5 border-0 shadow">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-brand-secondary text-dark px-3 py-1 rounded-pill small fw-bold mb-2">
                        Fitur Mendatang
                    </span>
                    <h3 class="fw-bold mb-2">Kalkulator Rute & Perhitungan Tarif Otomatis</h3>
                    <p class="text-white-50 mb-0 lh-base">
                        Pada pembaruan berikutnya, Anda dapat memilih titik jemput hotel, menyusun multi-destinasi wisata, dan melihat kalkulasi estimasi jarak, durasi, serta biaya sewa secara instan berbasis integrasi Google Routes API.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    @if ($brand['has_valid_whatsapp'])
                        <a href="{{ $brand['whatsapp_url'] }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="btn btn-brand-secondary btn-lg px-4 py-3 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp fs-5"></i>
                            <span>Tanya Rute via WhatsApp</span>
                        </a>
                    @else
                        <button type="button" 
                                class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill d-inline-flex align-items-center gap-2"
                                data-bs-toggle="modal" 
                                data-bs-target="#contactInfoModal">
                            <i class="bi bi-chat-dots fs-5"></i>
                            <span>Hubungi Layanan</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
