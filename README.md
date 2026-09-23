# Jumu Bali Tour - MVP Platform Layanan Private Tour Bali

Platform layanan sewa mobil dan private tour Bali berbasis web modern yang menghadirkan transparansi rute, perhitungan jarak riil & durasi jalan raya menggunakan Google Routes API, kalkulasi estimasi tarif transparan, serta pemesanan instan melalui WhatsApp Click-to-Chat.

---

## 1. Fitur Utama

### Pengunjung / Wisatawan (Public)
- **Eksplorasi Katalog Wisata:** Menjelajahi tempat wisata di Bali berdasarkan wilayah daerah (Kuta, Ubud, Seminyak, dll.) dan tema kategori (Pantai, Budaya, Alam).
- **Detail Destinasi Komprehensif:** Deskripsi daya tarik, alamat, koordinat peta, foto utama, dan rekomendasi wisata sekitar.
- **Kalkulator Rute & Peta Interaktif:**
  - Pemilihan titik penjemputan (populer atau koordinat manual).
  - Pemilihan 1 hingga maksimal 5 destinasi wisata per perjalanan tour.
  - Pengurutan urutan tujuan secara manual (tombol Naik/Turun) yang langsung tersinkronisasi dengan penomoran marker peta.
  - Visualisasi garis rute jalan raya (polyline) di Google Maps.
  - Perhitungan total jarak (km) dan estimasi durasi waktu tempuh jalan raya via Google Routes API.
  - Rincian estimasi biaya sewa transparan berbasis formula jarak dan jumlah penumpang (1–12 orang) dengan pembulatan rupiah (*ceil*).
- **Pemesanan Instan via WhatsApp:** Formulir reservasi cepat yang langsung dialihkan ke WhatsApp Click-to-Chat (`wa.me`) dengan template pesan rute perjalanan lengkap.
- **Responsif & Aksesibel:** Optimal pada layar ponsel pintar (320px–430px) hingga desktop tanpa horizontal scroll, serta mematuhi standar aksesibilitas WCAG 2.2 AA.

### Administrator Tour (Admin Panel)
- **Autentikasi Aman:** Login administrator tunggal terlindungi proteksi anti-brute-force rate limiting dan secure session.
- **Dashboard Statistik:** Ringkasan metrik data daerah, kategori, tempat wisata, dan status sistem.
- **CRUD Daerah (Regions):** Kelola data daerah wisata dengan slug otomatis dan perlindungan hapus berelasi (*restrictOnDelete*).
- **CRUD Kategori (Categories):** Kelola tema kategori tempat wisata.
- **CRUD Tempat Wisata (Destinations):** Kelola nama, deskripsi, alamat, koordinat presisi, upload/ganti/hapus foto utama (JPG/PNG/WEBP max 5MB), dan urutan tampilan.
- **Pencarian & Filter Terpadu:** Penyaringan cepat berdasarkan kata kunci, daerah, kategori, dan status publikasi.

---

## 2. Stack Teknologi
- **Framework:** Laravel 12.x
- **Bahasa:** PHP >= 8.2 (Target Produksi: PHP 8.3)
- **Basis Data:** MariaDB >= 10.4 / MySQL >= 8.0 (Eloquent ORM)
- **Frontend Template:** Laravel Blade + Bootstrap 5.3 CDN + Bootstrap Icons + Custom CSS
- **Interaksi Frontend:** Vanilla JavaScript ringan (bebas dependensi runtime Node.js di server hosting)
- **Integrasi Peta:** Google Maps JavaScript API (Frontend) & Google Routes API (Backend)
- **Integrasi Komunikasi:** WhatsApp Click-to-Chat (Stateless Protocol)
- **Target Hosting:** Apache cPanel Shared Hosting

---

## 3. Prasyarat Sistem
- PHP `>= 8.2.0` dengan ekstensi: `pdo_mysql`, `curl`, `fileinfo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `zip`, `gd`.
- Composer 2.x
- MySQL / MariaDB Server
- Web Server Apache dengan `mod_rewrite` aktif

---

## 4. Instalasi & Menjalankan di Lingkungan Lokal

1. **Clone repository:**
   ```bash
   git clone https://github.com/akun-anda/jumu_tour.git
   cd jumu_tour
   ```
2. **Instal dependensi Composer:**
   ```bash
   composer install
   ```
3. **Konfigurasi file `.env`:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Atur kredensial database di `.env`:**
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jumu_tour
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. **Jalankan migrasi dan seeder awal:**
   ```bash
   php artisan migrate --seed
   ```
6. **Buat symbolic link storage:**
   ```bash
   php artisan storage:link
   ```
7. **Jalankan server pengembangan lokal:**
   ```bash
   php artisan serve
   ```
   Akses di peramban: [http://localhost:8000](http://localhost:8000)

---

## 5. Kredensial Administrator Awal (Default Admin)
- **URL Login:** `http://localhost:8000/admin/login`
- **Email:** `admin@balitourservice.local`
- **Password:** `AdminJumu2026!`

---

## 6. Variabel Environment Kunci (`.env`)
```ini
APP_NAME="Jumu Bali Tour"
APP_ENV=local                  # Gunakan 'production' di cPanel
APP_DEBUG=true                 # Wajib 'false' di production
APP_URL=http://localhost:8000  # Gunakan HTTPS di production

# Google Maps API Configuration
GOOGLE_MAPS_BROWSER_KEY=       # Kunci browser untuk frontend (Restriksi HTTP Referrer)
GOOGLE_MAPS_SERVER_KEY=        # Kunci server untuk Routes API (Privat di .env)
GOOGLE_ROUTES_API_KEY=

# WhatsApp Business Configuration
WHATSAPP_PHONE_NUMBER=6281234567890
WHATSAPP_COUNTRY_CODE=62
```

---

## 7. Pengujian Otomatis (Automated Testing)
Eksekusi seluruh automated test suite:
```bash
php artisan test
```
*Status: 108 tests passed (351 assertions) 100% SUCCESS.*

---

## 8. Ringkasan Deployment ke cPanel Shared Hosting
1. Atur versi PHP di cPanel ke **PHP 8.3** via MultiPHP Manager.
2. Arahkan Document Root domain ke folder `/home/USERNAME/tour-bali-app/public` (Source code diletakkan di luar `public_html`).
3. Jalankan `composer install --no-dev --optimize-autoloader`.
4. Unggah atau buat file `.env` production dengan `APP_DEBUG=false` dan kredensial database cPanel.
5. Jalankan `php artisan migrate --force`.
6. Kompilasi cache production: `php artisan optimize`.
7. Panduan lengkap: [`docs/cpanel-deployment-guide.md`](docs/cpanel-deployment-guide.md) & [`docs/deployment-git-cpanel.md`](docs/deployment-git-cpanel.md).

---

## 9. Struktur Direktori Utama
```text
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Public & Admin Controllers
│   │   ├── Middleware/      # Admin Auth & Security Headers
│   │   └── Requests/        # Form Validation Requests
│   ├── Models/              # Eloquent Models (Destination, Region, dll)
│   └── Services/            # Business Logic (Route, Pricing, WhatsApp)
├── config/                  # Konfigurasi Aplikasi & Layanan
├── database/
│   ├── migrations/          # 9 File Migrasi Database
│   └── seeders/             # Initial Master Seeders
├── docs/                    # Dokumentasi Lengkap Sistem (13 Dokumen)
├── public/
│   ├── assets/css/          # custom.css & styling tema
│   ├── .htaccess            # Apache Rewrite & Dotfile Protection
│   └── index.php            # Front Controller
├── resources/
│   └── views/               # Blade Templates (Public, Admin, Errors)
├── routes/
│   └── web.php              # Controller-Based Routes
├── storage/                 # Upload Foto Destinasi, Cache, Logs
└── tests/                   # 108 Automated Tests (Unit & Feature)
```

---

## 10. Catatan Keamanan (Security Notes)
- Mengikuti pedoman **OWASP ASVS Baseline**.
- Proteksi brute force login admin via rate limiter `throttle:5,1`.
- Sanitasi input dan proteksi auto-escaping Blade terhadap XSS.
- Validasi tipe MIME file gambar upload ketat; file berekstensi skrip dan `.svg` ditolak.
- Pemisahan kunci API Google Maps (Browser vs Server key).
- Nilai harga dan jarak dari client tidak dipercaya; kalkulasi dihitung ulang 100% di server backend.
- Data pemesan WhatsApp tidak disimpan di database (*privacy by design*).

---

## 11. Indeks Dokumentasi Lengkap
- [Gambaran Umum Sistem (System Overview)](docs/system-overview.md)
- [Panduan Instalasi Lengkap (Installation Guide)](docs/installation.md)
- [Dokumentasi Basis Data & ERD (Database Guide)](docs/database.md)
- [Panduan Administrator (Admin Guide)](docs/admin-guide.md)
- [Panduan Pengguna / Wisatawan (User Guide)](docs/user-guide.md)
- [Dokumentasi Integrasi API Eksternal (API Integration)](docs/api-integration.md)
- [Panduan Deployment cPanel Lengkap (cPanel Deployment Guide)](docs/cpanel-deployment-guide.md)
- [Panduan Deployment Berulang Git cPanel (.cpanel.yml)](docs/deployment-git-cpanel.md)
- [Checklist Audit Keamanan OWASP ASVS (Security Checklist)](docs/security-checklist.md)
- [Checklist QA Responsivitas & Aksesibilitas](docs/responsive-accessibility-checklist.md)
- [Matriks Verifikasi Produksi (Production Smoke Test)](docs/production-smoke-test.md)
- [Lembar Uji Penerimaan Pengguna (User Acceptance Test)](docs/user-acceptance-test.md)
- [Batasan Sistem yang Diketahui (Known Limitations)](docs/known-limitations.md)
- [Catatan Rilis Produk (Release Notes v0.1.0)](docs/release-notes.md)

---

## 12. Lisensi & Penggunaan Asset
Aplikasi ini dikembangkan untuk MVP Website Layanan Tour Bali. Seluruh foto destinasi yang digunakan sebagai placeholder contoh ditujukan untuk tujuan demonstrasi MVP dan disarankan diganti dengan aset fotografi resmi milik pengelola layanan tour sebelum publikasi komersial.
# jumu_tour
