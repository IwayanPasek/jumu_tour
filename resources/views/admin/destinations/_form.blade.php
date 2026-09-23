<div class="row g-3">
    <!-- Nama Tempat Wisata -->
    <div class="col-md-7">
        <label for="name" class="form-label fw-semibold">
            Nama Tempat Wisata <span class="text-danger">*</span>
        </label>
        <input type="text" 
               name="name" 
               id="name" 
               class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name', $destination->name) }}" 
               placeholder="Contoh: Pantai Pandawa, Pura Uluwatu" 
               required 
               autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Nama objek atau daya tarik wisata.</div>
        @enderror
    </div>

    <!-- Slug URL -->
    <div class="col-md-5">
        <label for="slug" class="form-label fw-semibold">
            Slug URL <span class="text-muted fw-normal">(Opsional)</span>
        </label>
        <input type="text" 
               name="slug" 
               id="slug" 
               class="form-control @error('slug') is-invalid @enderror" 
               value="{{ old('slug', $destination->slug) }}" 
               placeholder="Biarkan kosong untuk otomatis">
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Otomatis dibuat dari nama jika dikosongkan.</div>
        @enderror
    </div>

    <!-- Daerah Wisata (Region) -->
    <div class="col-md-6">
        <label for="region_id" class="form-label fw-semibold">
            Daerah Wisata <span class="text-danger">*</span>
        </label>
        <select name="region_id" id="region_id" class="form-select @error('region_id') is-invalid @enderror" required>
            <option value="">-- Pilih Daerah --</option>
            @foreach($regions as $region)
                <option value="{{ $region->id }}" {{ old('region_id', $destination->region_id) == $region->id ? 'selected' : '' }}>
                    {{ $region->name }} {{ isset($region->is_active) && !$region->is_active ? '(Nonaktif)' : '' }}
                </option>
            @endforeach
        </select>
        @error('region_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Kawasan wilayah destinasi berada.</div>
        @enderror
    </div>

    <!-- Kategori Wisata (Category) -->
    <div class="col-md-6">
        <label for="category_id" class="form-label fw-semibold">
            Kategori Wisata <span class="text-muted fw-normal">(Opsional)</span>
        </label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">-- Tanpa Kategori / Umum --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $destination->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }} {{ isset($category->is_active) && !$category->is_active ? '(Nonaktif)' : '' }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Klasifikasi tema atau jenis wisata.</div>
        @enderror
    </div>

    <!-- Koordinat Latitude & Longitude (Input Manual) -->
    <div class="col-md-6">
        <label for="latitude" class="form-label fw-semibold">
            Titik Latitude <span class="text-danger">*</span>
        </label>
        <input type="text" 
               name="latitude" 
               id="latitude" 
               class="form-control font-monospace @error('latitude') is-invalid @enderror" 
               value="{{ old('latitude', $destination->latitude) }}" 
               placeholder="Contoh: -8.6830560" 
               required>
        @error('latitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Format desimal (-90 s/d 90). Untuk Bali umumnya berkisar antara <code>-8.0</code> s/d <code>-8.9</code>.</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="longitude" class="form-label fw-semibold">
            Titik Longitude <span class="text-danger">*</span>
        </label>
        <input type="text" 
               name="longitude" 
               id="longitude" 
               class="form-control font-monospace @error('longitude') is-invalid @enderror" 
               value="{{ old('longitude', $destination->longitude) }}" 
               placeholder="Contoh: 115.2288890" 
               required>
        @error('longitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Format desimal (-180 s/d 180). Untuk Bali umumnya berkisar antara <code>114.4</code> s/d <code>115.7</code>.</div>
        @enderror
    </div>

    <!-- Alamat Lengkap -->
    <div class="col-12">
        <label for="address" class="form-label fw-semibold">
            Alamat / Lokasi Spesifik
        </label>
        <input type="text" 
               name="address" 
               id="address" 
               class="form-control @error('address') is-invalid @enderror" 
               value="{{ old('address', $destination->address) }}" 
               placeholder="Contoh: Jl. Pantai Pandawa, Desa Kutuh, Kuta Selatan">
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Deskripsi Lengkap -->
    <div class="col-12">
        <label for="description" class="form-label fw-semibold">
            Deskripsi Destinasi
        </label>
        <textarea name="description" 
                  id="description" 
                  rows="4" 
                  class="form-control @error('description') is-invalid @enderror" 
                  placeholder="Informasi keindahan, daya tarik, jam buka, atau tips kunjungan...">{{ old('description', $destination->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Upload Gambar Utama -->
    <div class="col-12">
        <label for="image" class="form-label fw-semibold">
            Foto Utama Destinasi <span class="text-muted fw-normal">(JPG, PNG, WEBP maks 5 MB)</span>
        </label>
        <input type="file" 
               name="image" 
               id="image" 
               class="form-control @error('image') is-invalid @enderror" 
               accept="image/jpeg,image/png,image/webp">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Disarankan rasio landscape 16:9 atau 4:3 untuk tampilan optimal pada katalog.</div>
        @enderror

        <!-- Pratinjau Gambar Eksisting jika ada -->
        @if(!empty($destination->image_path))
            <div class="card mt-3 border bg-light p-3" style="max-width: 450px;">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($destination->image_path) }}" 
                         alt="{{ $destination->name }}" 
                         class="rounded object-fit-cover shadow-sm border" 
                         style="width: 120px; height: 80px;">
                    <div>
                        <div class="fw-semibold small text-dark mb-1">Foto Saat Ini</div>
                        <div class="text-muted small mb-2 text-truncate" style="max-width: 200px;">
                            {{ basename($destination->image_path) }}
                        </div>
                        @if($destination->exists)
                            <button type="button" 
                                    class="btn btn-outline-danger btn-sm py-0 px-2" 
                                    onclick="if(confirm('Hapus foto ini dari destinasi?')) { document.getElementById('delete-destination-image-form').submit(); }">
                                <i class="bi bi-trash me-1"></i>Hapus Foto
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Urutan Tampil (Display Order) -->
    <div class="col-md-6">
        <label for="display_order" class="form-label fw-semibold">
            Urutan Tampil (Display Order)
        </label>
        <input type="number" 
               name="display_order" 
               id="display_order" 
               class="form-control @error('display_order') is-invalid @enderror" 
               value="{{ old('display_order', $destination->display_order ?? 0) }}" 
               min="0" 
               max="999999">
        @error('display_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Angka lebih kecil tampil lebih awal (0, 1, 2, dst).</div>
        @enderror
    </div>

    <!-- Status Aktif -->
    <div class="col-md-6 d-flex align-items-center pt-md-3">
        <div class="form-check form-switch p-0 ps-5 mt-2">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input ms-n5 @error('is_active') is-invalid @enderror" 
                   type="checkbox" 
                   role="switch" 
                   id="is_active" 
                   name="is_active" 
                   value="1" 
                   {{ old('is_active', $destination->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">
                Status Publikasi Aktif
            </label>
            <div class="form-text">
                Jika dinonaktifkan, tempat wisata tidak akan muncul pada halaman publik.
            </div>
        </div>
        @error('is_active')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>
