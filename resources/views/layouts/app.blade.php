<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', e($brand['name']) . ' - ' . e($brand['tagline']))</title>
    <meta name="description" content="@yield('meta_description', e($brand['about']))">
    @stack('meta')

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

    <!-- Brand Dynamic Color Injection (Sanitized Hex Properties) -->
    <style>
        :root {
            --brand-primary: {{ $brand['primary_color'] }};
            --brand-secondary: {{ $brand['secondary_color'] }};
            --brand-accent: {{ $brand['accent_color'] }};
            --brand-text: {{ $brand['text_color'] }};
            --brand-muted: {{ $brand['muted_color'] }};
            --brand-surface: {{ $brand['surface_color'] }};
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column h-100">
    <!-- Accessibility Skip Link (WCAG 2.2) -->
    <a href="#main-content" class="skip-to-content">
        {{ __('common.skip_to_content') }}
    </a>

    <!-- Navbar Global -->
    @include('partials.navbar')

    <!-- Main Content Area -->
    <main class="flex-shrink-0" id="main-content">
        <!-- Flash Message Container -->
        <div class="container pt-3">
            @include('partials.flash-messages')
        </div>

        @yield('content')
    </main>

    <!-- Footer Global -->
    @include('partials.footer')

    <!-- Bootstrap 5.3 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>
