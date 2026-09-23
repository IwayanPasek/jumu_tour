# Panduan Lengkap Deployment Production ke cPanel (Apache / Shared Hosting)
**Proyek:** Website Layanan Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 15)  
**Target Lingkungan:** cPanel Shared Hosting (Apache, MultiPHP 8.3/8.2, MySQL/MariaDB)  
**Tanggal Terbit:** 23 September 2026  

---

## 1. Pre-Deployment Audit & Checklist Verifikasi Lokal
Sebelum menyentuh server cPanel atau mengunggah file apa pun, lakukan verifikasi ketat pada lingkungan lokal:

- [x] **Automated Tests:** Seluruh 108 automated test passed (351 assertions) tanpa kegagalan (`php artisan test`).
- [x] **Bebas Secret di Repository:** Tidak ada API Key Google Maps, kredensial basis data, password admin, atau secret key yang tertulis langsung pada source code.
- [x] **File `.env` Terisolasi:** File `.env`, `.env.backup`, `.env.production` terdaftar dalam `.gitignore` dan tidak di-track Git.
- [x] **Template `.env.example` Tersedia:** Menyediakan seluruh variabel environment yang dibutuhkan tanpa membocorkan nilai rahasia.
- [x] **`APP_DEBUG=false`:** Nilai debug diatur dinamis via environment variable dan wajib bernilai `false` di production.
- [x] **Restriksi API Key:** Browser API Key Google Maps hanya mengizinkan domain production terdaftar (*HTTP referrer restriction*). Server API Key Google Routes hanya ada di file `.env` production server.
- [x] **Database Terpisah:** Basis data lokal (`jumu_tour` di XAMPP) sepenuhnya terisolasi dan tidak tersambung ke database production hosting.
- [x] **Migrasi Final Teruji:** 9 migrasi database telah teruji urutan dan integritas referensialnya (`foreignKeyConstraints`).
- [x] **Seeder Terkendali:** Seeder demo tidak dieksekusi massal di production tanpa konfirmasi dan pemilahan kelas seeder.
- [x] **Izin Direktori Storage:** Direktori `storage/app/public/destinations` telah disiapkan untuk upload gambar tempat wisata.
- [x] **Halaman Error Kustom:** Tersedia template error HTTP 403, 404, 419, 429, dan 500 yang aman tanpa mengekspos stack trace.

---

## 2. Pemeriksaan Fitur & Kemampuan cPanel Hosting
Jangan mengasumsikan ketersediaan fitur otomatis pada shared hosting. Periksa menu-menu berikut di dasbor cPanel Anda:

### A. Versi PHP & Ekstensi (MultiPHP Manager / Select PHP Version)
1. Buka menu **MultiPHP Manager** atau **Select PHP Version** di cPanel.
2. Atur versi PHP domain ke **PHP 8.3** (Target Utama) atau **PHP 8.2** (Fallback).
3. Pastikan ekstensi-ekstensi PHP berikut berstatus aktif (*Centang / Enabled*):
   - `ctype`
   - `curl`
   - `fileinfo` (Wajib untuk validasi MIME type upload foto)
   - `mbstring`
   - `openssl`
   - `pdo`
   - `pdo_mysql` (Driver koneksi MariaDB/MySQL)
   - `tokenizer`
   - `xml`
   - `zip`
   - `gd` (Wajib untuk pemrosesan gambar dan thumbnail)

### B. Nilai Konfigurasi PHP (PHP INI Options)
Sesuaikan nilai PHP berikut pada menu **MultiPHP INI Editor** di cPanel:
- `memory_limit`: Minimal **128M** (Disarankan **256M** atau **512M**).
- `upload_max_filesize`: Minimal **10M** (Aplikasi membatasi upload foto max 5MB).
- `post_max_size`: Minimal **12M** (Lebih besar dari `upload_max_filesize`).
- `max_execution_time`: Minimal **60** detik (Standar 120 detik).

### C. Ketersediaan Alat Tambahan (SSH, Composer, Git, Symlink)
- **SSH / Terminal:** Periksa apakah terdapat ikon **Terminal** di bagian *Advanced*. Jika tidak ada, gunakan jalur **Opsi 2 (File Manager / Local Build)**.
- **Git Version Control:** Periksa menu **Git™ Version Control** di cPanel.
- **Symbolic Link Support:** Beberapa hosting mematikan fungsi `symlink()` karena alasan keamanan `open_basedir`. Jika dimatikan, gunakan jalur penempatan folder asset upload publik yang disetujui.

---

## 3. Persiapan Build Lokal (Local Build)
Karena shared hosting umumnya tidak memiliki Node.js, seluruh proses kompilasi dependensi dan asset frontend wajib diselesaikan di komputer lokal sebelum proses upload:

1. **Jalankan automated test lokal:**
   ```bash
   php artisan test
   ```
   *Pastikan 108 test passed 100%.*

2. **Bersihkan dev-dependencies Composer:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
   *Langkah ini membuang pustaka testing (Pest, PHPUnit, Mockery) dan mengoptimasi class loader untuk performa maksimal.*

3. **Kompilasi Asset Frontend (Jika Memakai Build Tool / Vite):**
   ```bash
   npm install
   npm run build
   ```
   *Pastikan direktori `public/build` terbentuk jika project menggunakan Vite. Pada project ini, CSS utama berada di `public/assets/css/custom.css` dan Bootstrap 5 menggunakan CDN berkecepatan tinggi.*

4. **Keluarkan `node_modules`:**
   *Jangan pernah mengunggah folder `node_modules` ke server cPanel karena hanya memboroskan kuota disk dan inode.*

---

## 4. Opsi Alur Deployment (Upload Project)

### Struktur Direktori yang Direkomendasikan
Untuk keamanan tingkat tinggi, source code Laravel **TIDAK BOLEH** diletakkan langsung di dalam `public_html`. Gunakan pemisahan direktori:

```text
/home/USERNAME/
├── tour-bali-app/                 <-- FOLDER ROOT PROJECT (Di Luar Public Web)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                    <-- Target Document Root
│   │   ├── assets/
│   │   ├── storage/ -> ../storage/app/public
│   │   ├── .htaccess
│   │   └── index.php
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                       <-- AMAN, Tidak Bisa Diakses dari Internet
│   └── artisan
└── public_html/                   <-- Jangan gunakan untuk source code utama jika bisa ubah Document Root
```

---

### OPSI 1: cPanel yang Memiliki Fitur SSH / Terminal & Composer

1. **Akses Server via SSH / Terminal:**
   Buka menu **Terminal** di cPanel atau akses via client SSH (PuTTY, Terminal macOS/Linux).

2. **Clone atau Upload Repository ke Folder Luar `public_html`:**
   ```bash
   cd ~
   git clone https://github.com/akun-anda/jumu_tour.git tour-bali-app
   # atau upload zip source code lalu ekstrak ke ~/tour-bali-app
   ```

3. **Masuk ke Direktori Project:**
   ```bash
   cd ~/tour-bali-app
   ```

4. **Instal Dependensi Production:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

5. **Buat File Environment Production (`.env`):**
   Salin template `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   nano .env
   # Masukkan kredensial database cPanel, URL HTTPS, dan API Key resmi
   ```
   Generate application key jika belum ada:
   ```bash
   php artisan key:generate
   ```

6. **Tautkan Storage Public (Symlink):**
   ```bash
   php artisan storage:link
   ```

7. **Migrasi Database:**
   ```bash
   php artisan migrate --force
   ```

8. **Inisialisasi Data Awal (Seeder Terkendali):**
   ```bash
   php artisan db:seed --class=AdminUserSeeder --force
   php artisan db:seed --class=PricingConfigurationSeeder --force
   php artisan db:seed --class=BrandSettingSeeder --force
   # Jika ingin memasukkan data awal daerah, kategori, dan destinasi Bali:
   php artisan db:seed --class=RegionSeeder --force
   php artisan db:seed --class=CategorySeeder --force
   php artisan db:seed --class=DestinationSeeder --force
   ```

9. **Kompilasi Cache Production:**
   ```bash
   php artisan optimize
   ```

10. **Arahkan Document Root Domain:**
    Masuk ke cPanel menu **Domains**, klik **Manage** pada domain Anda, dan ubah **Document Root** menjadi:
    `/home/USERNAME/tour-bali-app/public`

---

### OPSI 2: cPanel Standar Tanpa SSH / Terminal (Via File Manager)

Jika paket hosting Anda tidak menyediakan akses SSH atau Terminal:

1. **Siapkan Paket Deployment di Komputer Lokal:**
   - Jalankan `composer install --no-dev --optimize-autoloader` di lokal.
   - Buat file arsip ZIP dari seluruh project (namai `tour-bali-app.zip`).
   - **Kecualikan dari ZIP:** `.env`, `.git`, `node_modules`, `tests`.
   - **Sertakan:** Folder `vendor`, `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `artisan`, `composer.json`.

2. **Upload & Ekstrak via cPanel File Manager:**
   - Buka menu **File Manager** di cPanel.
   - Masuk ke direktori home (`/home/USERNAME/`).
   - Upload file `tour-bali-app.zip` langsung di home folder (sejajar dengan `public_html`, **BUKAN** di dalam `public_html`).
   - Ekstrak arsip ZIP ke folder baru bernama `/home/USERNAME/tour-bali-app`.

3. **Buat File `.env` Production:**
   - Di dalam folder `/home/USERNAME/tour-bali-app`, buat file baru bernama `.env`.
   - Isi dengan konfigurasi production lengkap (lihat Bagian 6 di bawah).

4. **Konfigurasi Document Root Domain:**
   - Masuk ke menu **Domains** di cPanel.
   - Ubah Document Root domain utama / addon domain Anda ke:
     `/home/USERNAME/tour-bali-app/public`

> [!WARNING]
> **Peringatan Penting Jika Document Root Tidak Dapat Diubah:**
> Jika provider hosting Anda mengunci document root hanya pada `public_html` dan tidak mengizinkan pengalihan folder:
> 1. Pindahkan isi dari `/home/USERNAME/tour-bali-app/public/` (seperti `index.php`, `.htaccess`, `assets/`, dll.) ke dalam `/home/USERNAME/public_html/`.
> 2. Buka file `/home/USERNAME/public_html/index.php`, sesuaikan path bootstrap Laravel:
>    ```php
>    require __DIR__.'/../tour-bali-app/vendor/autoload.php';
>    $app = require_once __DIR__.'/../tour-bali-app/bootstrap/app.php';
>    ```
> 3. **JANGAN PERNAH** memindahkan folder `app`, `bootstrap`, `config`, `.env`, atau `artisan` ke dalam `public_html`. Source code inti harus tetap berada di luar folder yang dapat diakses publik!

---

## 5. Konfigurasi Basis Data MySQL / MariaDB di cPanel

1. **Buat Database:**
   - Buka menu **MySQL® Databases** di cPanel.
   - Masukkan nama database (misal: `tourdb`). Nama lengkap di cPanel akan memiliki prefix, contoh: `usercpanel_tourdb`.
2. **Buat Pengguna Database:**
   - Di bagian *Add New User*, buat user baru (contoh: `usercpanel_tourusr`).
   - Buat password acak yang kuat (simpan di password manager).
3. **Hubungkan User ke Database:**
   - Pada bagian *Add User To Database*, pilih user dan database yang telah dibuat.
   - Klik **Add**, lalu centang **ALL PRIVILEGES**. Klik **Make Changes**.
4. **Eksekusi Migrasi:**
   - Melalui SSH: Jalankan `php artisan migrate --force`.
   - Melalui phpMyAdmin (Alternatif jika tanpa SSH): Anda dapat mengekspor struktur tabel dari database lokal (tanpa data sensitif lokal) dan mengimpornya via menu **phpMyAdmin** di cPanel.

> [!CAUTION]
> **Dilarang Menjalankan:**
> - `php artisan migrate:fresh`
> - `php artisan migrate:reset`
> - `php artisan db:wipe`
> Perintah di atas bersifat destruktif dan akan menghapus seluruh data tabel yang ada di production!

---

## 6. Template Konfigurasi `.env` Production

Buat file `.env` di folder `/home/USERNAME/tour-bali-app/.env` dengan susunan berikut:

```ini
APP_NAME="Bali Tour Service"
APP_ENV=production
APP_KEY=base64:GENERATE_KEY_KHUSUS_PRODUCTION_ANDA=
APP_DEBUG=false
APP_URL=https://domain-resmi-anda.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usercpanel_tourdb
DB_USERNAME=usercpanel_tourusr
DB_PASSWORD=PasswordDatabaseKuatAnda123!

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file

# Google Maps API Configuration
# Browser key dibatasi hanya untuk HTTP Referrer: https://domain-resmi-anda.com/*
GOOGLE_MAPS_BROWSER_KEY=AIzaSyD_ContohBrowserKeyKhususDomain
# Server key hanya digunakan di backend Laravel untuk Routes API
GOOGLE_MAPS_SERVER_KEY=AIzaSyB_ContohServerRoutesKeyRahasia
GOOGLE_ROUTES_API_KEY=AIzaSyB_ContohServerRoutesKeyRahasia

# WhatsApp Business Integration
WHATSAPP_PHONE_NUMBER=6281234567890
WHATSAPP_COUNTRY_CODE=62

# Kredensial Administrator Awal (Ganti segera setelah login pertama)
ADMIN_NAME="Administrator Utama"
ADMIN_EMAIL=admin@domain-resmi-anda.com
ADMIN_PASSWORD=PasswordAdminSangatKuatDanUnik2026!
```

---

## 7. Penanganan File Storage & Upload Foto Wisata

1. **Hak Akses Folder (*Permissions*):**
   - Pastikan folder `storage/` dan `bootstrap/cache/` memiliki permission **755** (direktori) dan **644** (file).
   - Jangan pernah memberikan permission **777** karena membuka celah keamanan serius pada server shared hosting.

2. **Membuat Symbolic Link Storage:**
   - **Jika SSH tersedia:**
     ```bash
     php artisan storage:link
     ```
   - **Jika Symlink Dilarang oleh Hosting:**
     Buat script PHP sekali jalan bernama `symlink.php` di dalam folder `public/`:
     ```php
     <?php
     symlink('/home/USERNAME/tour-bali-app/storage/app/public', '/home/USERNAME/tour-bali-app/public/storage');
     echo "Symlink created successfully!";
     ```
     Buka script tersebut di browser sekali (`https://domain-anda.com/symlink.php`), lalu **SEGERA HAPUS** file `symlink.php` dari server.

3. **Proteksi Direktori Upload:**
   Pastikan di dalam folder `storage/app/public/destinations` tidak ada file berekstensi `.php`, `.phtml`, atau `.sh`. Validasi form request aplikasi telah mengunci ekstensi hanya untuk `jpg`, `jpeg`, `png`, `webp` (MIME images) maksimal 5MB.

---

## 8. Optimalisasi Cache Production (Optimize)

Setelah seluruh konfigurasi `.env` dan database terverifikasi benar, jalankan perintah caching berikut untuk mempercepat waktu respon (*response time*) aplikasi:

```bash
# Melalui SSH / Terminal:
php artisan optimize

# Atau perintah terpisah:
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> [!NOTE]
> **Prosedur Pembaruan Konfigurasi:**
> Jika di kemudian hari Anda mengubah nilai pada `.env`, Anda wajib membersihkan dan memperbarui cache:
> ```bash
> php artisan optimize:clear
> php artisan optimize
> ```

---

## 9. Konfigurasi HTTPS & Restriksi API Key

1. **Penerbitan Sertifikat SSL:**
   - Aktifkan fitur **Let's Encrypt SSL** atau **cPanel AutoSSL** pada menu *SSL/TLS Status*.
   - Aktifkan opsi **Force HTTPS Redirect** di menu *Domains* cPanel.

2. **Restriksi Google Maps Browser Key:**
   - Buka Google Cloud Console > APIs & Services > Credentials.
   - Edit `GOOGLE_MAPS_BROWSER_KEY`:
     - **Application restrictions:** Pilih *Websites*.
     - **Website restrictions:** Tambahkan:
       - `https://domain-resmi-anda.com/*`
       - `https://www.domain-resmi-anda.com/*`
   - Ini mencegah pihak lain mencuri API key untuk digunakan di situs luar.

3. **Restriksi Google Maps Server Key (Google Routes API):**
   - Edit `GOOGLE_MAPS_SERVER_KEY`:
     - **API restrictions:** Batasi hanya untuk *Routes API*.
     - **IP restrictions:** (Opsional) Masukkan IP server shared hosting Anda.

---

## 10. Prosedur Rollback Dasar (Rollback Plan)

Jika setelah proses deployment ditemukan kegagalan fatal pada fungsi kalkulator, database, atau autentikasi admin:

1. **Aktifkan Maintenance Mode Segera:**
   ```bash
   php artisan down --secret="kunci-rahasia-bypass"
   ```
2. **Restore Source Code Versi Sebelumnya:**
   - Jangan hapus backup versi rilis lama sebelum rilis baru lulus smoke test 100%.
   - Ganti folder aplikasi dengan folder rilis cadangan (`tour-bali-app-backup`).
3. **Restore Database:**
   - Jika migrasi baru bersifat destruktif atau gagal, import kembali file `.sql` cadangan yang dibuat sebelum proses deployment melalui menu phpMyAdmin.
4. **Bersihkan dan Bangun Ulang Cache:**
   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```
5. **Nonaktifkan Maintenance Mode:**
   ```bash
   php artisan up
   ```
6. **Eksekusi Ulang Smoke Test** pada seluruh 30 skenario untuk memastikan sistem kembali normal.
