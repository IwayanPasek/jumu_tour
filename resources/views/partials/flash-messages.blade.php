@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
        <div class="flex-grow-1">
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
        <div class="flex-grow-1">
            {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-3" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5 text-warning"></i>
        <div class="flex-grow-1">
            {{ session('warning') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-3" role="alert">
        <i class="bi bi-info-circle-fill me-2 fs-5 text-info"></i>
        <div class="flex-grow-1">
            {{ session('info') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-x-circle-fill me-2 fs-5 text-danger"></i>
            <strong>Terdapat kesalahan pada isian:</strong>
        </div>
        <ul class="mb-0 ps-4 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif
