<div class="row g-3">
    <!-- Nama Kategori -->
    <div class="col-12">
        <label for="name" class="form-label fw-semibold">
            Nama Kategori <span class="text-danger">*</span>
        </label>
        <input type="text" 
               name="name" 
               id="name" 
               class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name', $category->name) }}" 
               placeholder="Contoh: Pantai, Budaya & Pura, Alam & Air Terjun, Petualangan" 
               required 
               autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Nama kelompok atau tema destinasi wisata.</div>
        @enderror
    </div>

    <!-- Slug URL -->
    <div class="col-12">
        <label for="slug" class="form-label fw-semibold">
            Slug URL <span class="text-muted fw-normal">(Opsional)</span>
        </label>
        <input type="text" 
               name="slug" 
               id="slug" 
               class="form-control @error('slug') is-invalid @enderror" 
               value="{{ old('slug', $category->slug) }}" 
               placeholder="Biarkan kosong untuk generate otomatis dari nama kategori">
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Slug digunakan sebagai URL publik (contoh: <code>/categories/pantai</code>). Jika dikosongkan, sistem akan otomatis membuatnya.</div>
        @enderror
    </div>

    <!-- Deskripsi -->
    <div class="col-12">
        <label for="description" class="form-label fw-semibold">
            Deskripsi Kategori
        </label>
        <textarea name="description" 
                  id="description" 
                  rows="4" 
                  class="form-control @error('description') is-invalid @enderror" 
                  placeholder="Ceritakan gambaran singkat kategori wisata ini...">{{ old('description', $category->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Status Aktif -->
    <div class="col-12">
        <div class="form-check form-switch p-0 ps-5 mt-2">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input ms-n5 @error('is_active') is-invalid @enderror" 
                   type="checkbox" 
                   role="switch" 
                   id="is_active" 
                   name="is_active" 
                   value="1" 
                   {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">
                Status Aktif
            </label>
            <div class="form-text">
                Jika dinonaktifkan, kategori ini tidak akan tampil pada navigasi atau katalog publik.
            </div>
        </div>
        @error('is_active')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>
