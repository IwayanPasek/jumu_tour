<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ $brand['name'] ?? 'Bali Tour Service' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <style>
        .admin-sidebar {
            background-color: var(--brand-primary);
            width: 100%;
        }
        @media (min-width: 992px) {
            .admin-sidebar {
                width: 260px;
                min-height: 100vh;
            }
        }
        .admin-nav-link {
            color: rgba(255, 255, 255, 0.7);
            border-radius: 0.5rem;
            padding: 0.65rem 1rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .admin-nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
        }
        .admin-nav-link.active {
            color: #ffffff;
            background-color: var(--brand-secondary);
            color: #0f172a !important;
            font-weight: 600;
        }
        .admin-nav-link.disabled {
            color: rgba(255, 255, 255, 0.35);
            pointer-events: none;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-light d-flex flex-column h-100">
    <!-- Aksesibilitas WCAG: Skip to Main Content -->
    <a href="#admin-main-content" class="skip-to-content">Lewati ke Konten Admin</a>

    <div class="d-flex flex-column flex-lg-row min-vh-100">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar d-flex flex-column p-3 text-white flex-shrink-0">
            <!-- Brand Info & Mobile Toggler -->
            <div class="d-flex align-items-center justify-content-between mb-lg-4 pb-3 border-bottom border-secondary border-opacity-25 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2 bg-brand-secondary text-dark">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold mb-0 text-white">{{ $brand['name'] ?? 'Bali Tour Service' }}</h6>
                        <small class="text-white-50" style="font-size: 0.75rem;">Panel Administrator</small>
                    </div>
                </div>
                <!-- Mobile Toggler Button -->
                <button class="btn btn-outline-light btn-sm d-lg-none" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#adminSidebarCollapse" 
                        aria-controls="adminSidebarCollapse" 
                        aria-expanded="false" 
                        aria-label="Buka navigasi panel admin">
                    <i class="bi bi-list fs-5"></i>
                </button>
            </div>

            <!-- Navigation Links & Actions Collapse Wrapper -->
            <div class="collapse d-lg-flex flex-column flex-grow-1" id="adminSidebarCollapse">

            <!-- Navigation Links -->
            <ul class="nav nav-pills flex-column gap-1 mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.regions.index') }}" 
                       class="admin-nav-link {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt"></i>
                        <span>Kelola Daerah</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>Kelola Kategori</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.destinations.index') }}" 
                       class="admin-nav-link {{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}">
                        <i class="bi bi-pin-map"></i>
                        <span>Kelola Destinasi</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="admin-nav-link disabled justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-cash-stack"></i>
                            <span>Pengaturan Tarif</span>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary small py-0 px-1" style="font-size: 0.65rem;">Segera</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="admin-nav-link disabled justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-palette"></i>
                            <span>Pengaturan Brand</span>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary small py-0 px-1" style="font-size: 0.65rem;">Segera</span>
                    </a>
                </li>
            </ul>

            <hr class="border-secondary border-opacity-25 my-3">

            <!-- Bottom Action: Home & Logout -->
            <div class="d-flex flex-column gap-2 px-2">
                <a href="{{ route('home') }}" target="_blank" class="admin-nav-link text-white-50">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Kunjungi Website</span>
                </a>
                
                <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="admin-nav-link w-100 text-start border-0 bg-transparent text-danger">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-grow-1 d-flex flex-column">
            <!-- Topbar Header -->
            <header class="navbar navbar-expand bg-white border-bottom shadow-sm px-4 py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <span class="text-muted small">Status Sistem:</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">
                            <i class="bi bi-check-circle me-1"></i>Administrator Terautentikasi
                        </span>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-semibold text-dark small">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="rounded-circle bg-brand-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content Body -->
            <main class="p-4 flex-grow-1" id="admin-main-content">
                @include('partials.flash-messages')
                @yield('content')
            </main>

            <!-- Admin Footer -->
            <footer class="bg-white border-top py-3 px-4 text-muted small text-center text-md-start">
                &copy; {{ date('Y') }} {{ $brand['name'] ?? 'Bali Tour Service' }} - Panel Administrator MVP.
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>
