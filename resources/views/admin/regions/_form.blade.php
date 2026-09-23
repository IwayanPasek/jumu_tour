<div class="row g-3">
    <!-- Nama Daerah -->
    <div class="col-md-7">
        <label for="name" class="form-label fw-semibold">
            Nama Daerah <span class="text-danger">*</span>
        </label>
        <input type="text" 
               name="name" 
               id="name" 
               class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name', $region->name) }}" 
               placeholder="Contoh: Ubud, Kuta, Nusa Penida" 
               required 
               autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Nama kawasan atau destinasi daerah di Bali.</div>
        @enderror
    </div>

    <!-- Kabupaten / Kota -->
    <div class="col-md-5">
        <label for="regency" class="form-label fw-semibold">
            Kabupaten / Kota
        </label>
        <input type="text" 
               name="regency" 
               id="regency" 
               class="form-control @error('regency') is-invalid @enderror" 
               value="{{ old('regency', $region->regency) }}" 
               placeholder="Contoh: Gianyar, Badung, Klungkung">
        @error('regency')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Nama kabupaten wilayah administratif.</div>
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
               value="{{ old('slug', $region->slug) }}" 
               placeholder="Biarkan kosong untuk generate otomatis dari nama daerah">
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Slug digunakan sebagai URL publik (contoh: <code>/regions/ubud</code>). Jika dikosongkan, sistem akan otomatis membuatnya.</div>
        @enderror
    </div>

    <!-- Deskripsi -->
    <div class="col-12">
        <label for="description" class="form-label fw-semibold">
            Deskripsi Daerah
        </label>
        <textarea name="description" 
                  id="description" 
                  rows="4" 
                  class="form-control @error('description') is-invalid @enderror" 
                  placeholder="Ceritakan gambaran singkat keunikan, daya tarik wisata, atau lokasi daerah ini...">{{ old('description', $region->description) }}</textarea>
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
                   {{ old('is_active', $region->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">
                Status Aktif
            </label>
            <div class="form-text">
                Jika dinonaktifkan, daerah dan destinasi di dalamnya tidak akan tampil pada katalog publik.
            </div>
        </div>
        @error('is_active')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>
