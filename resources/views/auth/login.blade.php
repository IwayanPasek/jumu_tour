<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('auth.login_title') }} - {{ $brand['name'] ?? 'Bali Tour Service' }}</title>

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
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        .login-card {
            border-radius: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4 position-relative">
    <div class="position-absolute top-0 end-0 p-3">
        <x-language-switcher />
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <div class="badge rounded-circle p-3 text-dark bg-brand-secondary shadow-lg mb-2">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-1">{{ __('admin.portal_title') }}</h3>
                    <p class="text-white-50 small mb-0">{{ $brand['name'] ?? 'Bali Tour Service' }}</p>
                </div>

                <div class="card login-card shadow-lg p-4 p-sm-5 bg-white">
                    <!-- Flash Message -->
                    @include('partials.flash-messages')

                    <form method="POST" action="{{ route('admin.login.store') }}" novalidate>
                        @csrf

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-dark small">{{ __('auth.email_label') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="admin@example.com" 
                                       required 
                                       autofocus 
                                       autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-dark small">{{ __('auth.password_label') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="bi bi-key"></i>
                                </span>
                                <input type="password" 
                                       class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="••••••••" 
                                       required 
                                       autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label text-muted small" for="remember">
                                {{ __('auth.remember_me') }}
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-brand-primary w-100 py-2 fw-semibold rounded-pill shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('auth.login_button') }}
                        </button>
                    </form>

                    <hr class="my-4 text-muted opacity-25">

                    <div class="text-center">
                        <a href="{{ route('home') }}" class="text-decoration-none text-muted small hover-text-dark d-inline-flex align-items-center gap-1">
                            <i class="bi bi-arrow-left"></i>
                            <span>{{ __('auth.back_to_public') }}</span>
                        </a>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-white-50 small mb-0">
                        &copy; {{ date('Y') }} {{ $brand['name'] ?? 'Bali Tour Service' }}. {{ __('auth.restricted_access') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
