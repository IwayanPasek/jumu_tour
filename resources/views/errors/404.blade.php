@extends('layouts.app')

@section('title', __('errors.404_title') . ' - ' . ($brandSetting->brand_name ?? 'Jumu Bali Tour'))

@section('content')
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="p-4 p-md-5 rounded-4 shadow-sm bg-white border">
                <div class="text-brand-primary mb-3">
                    <i class="bi bi-geo-alt-fill display-1"></i>
                </div>
                <h1 class="h2 fw-bold text-dark mb-2">{{ __('errors.404_title') }} (404)</h1>
                <p class="text-muted mb-4 lead fs-6">
                    {{ __('errors.404_desc') }}
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('home') }}" class="btn btn-brand-primary px-4 py-2 rounded-pill">
                        <i class="bi bi-house-door me-1"></i> {{ __('common.back_to_home') }}
                    </a>
                    <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                        <i class="bi bi-compass me-1"></i> {{ __('nav.destinations') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
