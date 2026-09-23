# Dokumentasi Internationalization (i18n) & English-First UI
**Project**: Jumu Bali Tour MVP  
**Cluster**: Cluster 18  
**Default Language**: English (`en`)  
**Alternative Language**: Bahasa Indonesia (`id`)  
**Timezone**: `Asia/Makassar` (WITA / UTC+8)  
**Currency**: Rupiah Indonesia (`IDR` / `Rp`)  

---

## 1. Ikhtisar & Arsitektur Localization
Sistem website Jumu Bali Tour MVP dirancang dengan prinsip **English-First UI** untuk menjangkau wisatawan mancanegara sebagai target audiens utama, dengan tetap menyediakan **Bahasa Indonesia** secara lengkap dan mudah diakses bagi wisatawan domestik maupun administrator lokal.

Semua teks antarmuka (UI strings) dikelola secara terpusat melalui file translasi Laravel (`lang/en.json`, `lang/id.json`, `lang/en/validation.php`, dan `lang/id/validation.php`) tanpa hardcoding teks antarmuka di dalam Blade template.

```
                    +---------------------------+
                    | Incoming HTTP Request     |
                    +-------------+-------------+
                                  |
                                  v
                    +---------------------------+
                    | Middleware: SetLocale     |
                    +-------------+-------------+
                                  |
         +------------------------+------------------------+
         |                        |                        |
         v                        v                        v
  Query Param ?lang=       Session: 'locale'        Cookie: 'locale'
         |                        |                        |
         +------------------------+------------------------+
                                  |
                                  v
                    +---------------------------+
                    | Whitelist Check (en, id)  |
                    +-------------+-------------+
                                  |
                   [Valid] /      \ [Invalid / Empty]
                          /        \
                         v          v
                  App::setLocale()  Fallback to 'en'
                         |
                         v
                  Set Carbon Locale
```

---

## 2. Bahasa yang Didukung, Default, & Fallback
- **Supported Locales**: `en` (English), `id` (Bahasa Indonesia).
- **Default Locale**: `en` (Dikonfigurasi di `config/app.php` dan `.env` melalui `APP_LOCALE=en`).
- **Fallback Locale**: `en` (Dikonfigurasi melalui `APP_FALLBACK_LOCALE=en`). Jika suatu kunci translasi belum tersedia di file bahasa yang dipilih, sistem otomatis menyajikan versi bahasa Inggris tanpa menghasilkan error.
- **Faker Locale**: `en_US` (Untuk testing dan database seeder).

---

## 3. Struktur File Translasi
Direktori translasi ditempatkan pada root project di folder `lang/`:

```
lang/
├── en.json                  # Kamus terjemahan antarmuka Bahasa Inggris (JSON key-value)
├── id.json                  # Kamus terjemahan antarmuka Bahasa Indonesia (JSON key-value)
├── en/
│   └── validation.php       # Pesan error validasi form Laravel (English)
└── id/
    └── validation.php       # Pesan error validasi form Laravel (Bahasa Indonesia)
```

### Konvensi Kunci Translasi (`JSON Keys`):
Kunci translasi menggunakan format namespace titik (`dot.notation`) yang logis:
- `nav.*`: Menu navigasi publik dan admin.
- `hero.*`: Judul, pengantar, dan tombol call-to-action utama.
- `home.*`: Kartu fitur, nilai utama, armada, dan rute di beranda.
- `destinations.*`: Halaman katalog, filter, pencarian, dan detail objek wisata.
- `regions.*`: Halaman daftar daerah dan detail per kabupaten.
- `categories.*`: Halaman kategori wisata tematik.
- `calculator.*`: Komponen kalkulator multi-destinasi, koordinat pickup, dan rincian tarif.
- `booking.*`: Formulir reservasi WhatsApp Click-to-Chat.
- `common.*`: Tombol umum (Simpan, Batal, Edit, Hapus, Filter, dsb).
- `admin.*`: Panel dashboard administrator dan manajemen data.
- `auth.*`: Halaman autentikasi login administrator.
- `footer.*`: Informasi jam kerja, hak cipta, dan navigasi bawah.
- `errors.*`: Pesan halaman HTTP status (403, 404, 419, 429, 500).

---

## 4. Mekanisme Middleware `SetLocale`
Middleware `App\Http\Middleware\SetLocale` didaftarkan pada grup middleware `web` di `bootstrap/app.php`. Urutan prioritas penentuan locale pada setiap request:
1. **Query Parameter `?lang={locale}`**: Jika ada dan valid (`en` atau `id`), locale disimpan ke session dan cookie, lalu diaplikasikan.
2. **Session `locale`**: Jika session sudah menyimpan preferensi bahasa pengguna.
3. **Cookie `locale`**: Jika session baru dibuka kembali tetapi pengguna sebelumnya memiliki cookie preferensi bahasa.
4. **Fallback Default**: Menggunakan `config('app.locale', 'en')`.
5. **Sinkronisasi Carbon**: Carbon date library disinkronkan via `\Carbon\Carbon::setLocale($locale)` agar format tanggal sesuai konteks bahasa aktif.

---

## 5. Komponen Language Switcher
Komponen Blade `<x-language-switcher />` (`resources/views/components/language-switcher.blade.php`) menyajikan dropdown pengubah bahasa:
- **Aksesibilitas WCAG 2.2**:
  - Menggunakan elemen `<button>` semantic dengan atribut `aria-expanded` dan `aria-label`.
  - Dilengkapi teks bahasa yang jelas (`English` / `Bahasa Indonesia`) dan icon bendera/globe.
  - Opsi bahasa yang sedang aktif ditandai dengan checkmark (`bi-check2`) dan atribut `aria-current="true"`.
- **Penempatan**:
  - Navbar Publik (`resources/views/partials/navbar.blade.php`).
  - Layout Admin Sidebar & Topbar (`resources/views/layouts/admin.blade.php`).
  - Halaman Login Admin (`resources/views/auth/login.blade.php`).
  - Footer Publik sebagai tautan alternatif.

---

## 6. Route & Controller Language Switcher
- **Endpoint**: `GET /language/{locale}` (`route('language.switch')`).
- **Controller**: `App\Http\Controllers\LanguageController@switch`.
- **Proteksi Keamanan**:
  - **Whitelist Validation**: Hanya menerima nilai `in:en,id`. Nilai lain otomatis dialihkan ke fallback default `en`.
  - **Open-Redirect Protection**: Memvalidasi URL asal (`referer`). Jika referer mengarah ke domain luar (external phishing/untrusted host), sistem otomatis mengarahkan ke `route('home')`.
  - **Session & Cookie Persistence**: Menyimpan locale terpilih ke `session(['locale' => $locale])` dan mengembalikan cookie terenkripsi selama 1 tahun (525.600 menit).

---

## 7. Format WhatsApp Bilingual (`WhatsAppService`)
Layanan `App\Services\WhatsAppService::formatTourBookingMessage()` secara otomatis menyesuaikan template teks reservasi dengan bahasa yang sedang aktif:

### Contoh Output Bahasa Inggris (`locale=en`):
```text
*CUSTOM TOUR BOOKING RESERVATION*
Bali Tour Service

*CUSTOMER DETAILS*
• Name: John Smith
• Phone/WA: +628123456789
• Travel Date: 15-10-2026
• Passengers: 2 Person(s)

*TOUR ITINERARY*
• Pickup Location:
  Ngurah Rai International Airport (DPS)
• Destinations:
  1. Uluwatu Temple
  2. Tanah Lot

*TRIP & PRICING ESTIMATION*
• Total Road Distance: 65.5 km
• Estimated Road Duration: 2 hours 15 mins
• Estimated Total Fare: Rp750.000

*SPECIAL REQUESTS / NOTES*
Need English-speaking driver and child seat

_Sent via Bali Tour Service Custom Itinerary Planner._
```

### Contoh Output Bahasa Indonesia (`locale=id`):
```text
*FORMAT RESERVASI TOUR BALI*
Jumu Bali Tour

*DATA PELANGGAN*
• Nama: Budi Santoso
• No. WhatsApp: 08123456789
• Tanggal Tour: 15-10-2026
• Jumlah Penumpang: 2 Orang

*RUTE PERJALANAN*
• Titik Jemput:
  Bandara Internasional I Gusti Ngurah Rai (DPS)
• Runtutan Destinasi:
  1. Pura Uluwatu
  2. Pura Tanah Lot

*ESTIMASI PERJALANAN & BIAYA*
• Total Jarak: 65.5 km
• Estimasi Durasi: 2 jam 15 menit
• Estimasi Biaya: Rp750.000

*CATATAN KHUSUS*
Mohon supir yang ramah dan siap jam 8 pagi

_Pesan ini dibuat otomatis melalui Kalkulator Rute Wisata._
```

---

## 8. Batasan & Konten yang Tidak Diterjemahkan Otomatis
Sesuai batasan teknis arsitektur:
1. **Nama Objek Wisata**: Konten database seperti *"Tanah Lot"*, *"Pura Tirta Empul Tampaksiring"*, *"Tegalalang Rice Terrace"* disajikan apa adanya sesuai entri database.
2. **Nama Brand & Tagline**: Dikelola melalui konfigurasi `BrandSetting` di database/admin.
3. **Koordinat Geografis**: Angka latitude dan longitude bersifat universal.
4. **Mata Uang & Angka**: Tetap berstandar `IDR` (`Rp`) sesuai regulasi dan operasional supir tour Bali.
5. **Respons Mentah API Google Maps**: Polyline dan koordinat dari Google Routes API tidak dimodifikasi.

---

## 9. Panduan Menambahkan Bahasa Baru di Masa Depan
Jika di masa mendatang ingin menambahkan bahasa lain (misal: Mandarin `zh` atau Jepang `ja`):
1. Tambahkan kode bahasa ke konfigurasi `config/app.php` pada array `supported_locales`:
   ```php
   'supported_locales' => ['en', 'id', 'zh'],
   ```
2. Buat file translasi antarmuka baru di `lang/zh.json`.
3. Buat folder validasi di `lang/zh/validation.php`.
4. Tambahkan opsi bahasa pada komponen `<x-language-switcher />`.
5. Tambahkan blok frasa WhatsApp pada `WhatsAppService::formatTourBookingMessage()`.
6. Jalankan pembersihan cache:
   ```bash
   php artisan optimize:clear
   ```

---

## 10. Pengoperasian Caching di Production (cPanel)
Ketika melakukan pembaruan file bahasa di cPanel, jalankan command berikut melalui Terminal atau SSH:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Jika SSH tidak tersedia pada cPanel, pembersihan cache dapat dilakukan dengan menghapus file di `bootstrap/cache/` (kecuali file `.gitignore`).

---

## 11. Daftar File yang Terlibat
1. `config/app.php` - Definisi locale default (`en`), fallback (`en`), dan whitelist (`supported_locales`).
2. `.env` & `.env.example` - Environment variable `APP_LOCALE=en`, `APP_FALLBACK_LOCALE=en`, `APP_SUPPORTED_LOCALES=en,id`.
3. `app/Http/Middleware/SetLocale.php` - Middleware pendeteksi preferensi bahasa.
4. `app/Http/Controllers/LanguageController.php` - Pengendali perpindahan bahasa dan proteksi open-redirect.
5. `bootstrap/app.php` - Pendaftaran middleware ke grup `web`.
6. `routes/web.php` - Route `GET /language/{locale}`.
7. `lang/en.json` - Kamus UI Bahasa Inggris.
8. `lang/id.json` - Kamus UI Bahasa Indonesia.
9. `lang/en/validation.php` & `lang/id/validation.php` - Pesan validasi form bilingual.
10. `app/Services/WhatsAppService.php` - Format template pesan WhatsApp bilingual.
11. `resources/views/components/language-switcher.blade.php` - Komponen pemilih bahasa.
12. `resources/views/partials/navbar.blade.php`, `footer.blade.php`, `layouts/app.blade.php`, `layouts/admin.blade.php` - Tampilan UI terintegrasi i18n.
13. `tests/Feature/LocalizationTest.php` - Automated test suite untuk verifikasi i18n.
