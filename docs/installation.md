# Panduan Instalasi & Konfigurasi (Installation Guide)
**Proyek:** Website Layanan Private Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 17)  
**Target Environment:** Komputer Pengembang Lokal (Windows / macOS / Linux) & Web Hosting (cPanel)  

---

## 1. Prasyarat Sistem (System Requirements)
Pastikan komputer pengembang atau server hosting Anda memenuhi spesifikasi berikut:
- **PHP:** Versi `>= 8.2.0` (Sangat disarankan **PHP 8.3**).
- **Ekstensi PHP Wajib:**
  - `pdo_mysql` (Koneksi basis data MySQL/MariaDB)
  - `curl` (Komunikasi HTTP server-to-server ke Google Routes API)
  - `fileinfo` (Validasi tipe MIME file gambar upload)
  - `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `zip`
  - `gd` (Pemrosesan gambar destinasi)
- **Basis Data:** MariaDB `>= 10.4` atau MySQL `>= 8.0`.
- **Composer:** Versi `2.x`.
- **Web Server:** Apache 2.4 dengan modul `mod_rewrite` aktif.

---

## 2. Langkah Instalasi di Lingkungan Lokal (Local Development)

### Langkah 1: Clone Repository
Buka terminal dan clone repository ke komputer lokal Anda:
```bash
git clone https://github.com/akun-anda/jumu_tour.git jumu_tour
cd jumu_tour
```

### Langkah 2: Instal Dependensi Composer
```bash
composer install
```

### Langkah 3: Konfigurasi File Environment (`.env`)
Salin template environment bawaan:
```bash
# Windows PowerShell:
copy .env.example .env

# Linux / macOS:
cp .env.example .env
```
Generate kunci enkripsi aplikasi:
```bash
php artisan key:generate
```

### Langkah 4: Konfigurasi Basis Data Lokal
Buka file `.env` dan sesuaikan koneksi database MySQL/MariaDB lokal Anda (misal: via XAMPP):
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jumu_tour
DB_USERNAME=root
DB_PASSWORD=
```
Buat database baru di MySQL lokal dengan nama `jumu_tour`.

### Langkah 5: Eksekusi Migrasi & Data Seeder Awal
Jalankan migrasi database beserta data master awal (daerah, kategori, destinasi Bali, paket tarif dasar, brand setting, dan akun admin):
```bash
php artisan migrate --seed
```

### Langkah 6: Tautkan Direktori Penyimpanan Publik (Storage Link)
Buat symbolic link untuk folder upload foto destinasi:
```bash
php artisan storage:link
```

### Langkah 7: Masukkan Kunci Google Maps API (Opsional untuk Fitur Peta)
Buka file `.env` dan tambahkan kunci API Google Anda jika ingin menguji peta & rute interaktif:
```ini
# Kunci browser untuk render peta JavaScript:
GOOGLE_MAPS_BROWSER_KEY=AIzaSy...

# Kunci server untuk kalkulasi jarak jalan raya Google Routes:
GOOGLE_MAPS_SERVER_KEY=AIzaSy...
```
*(Catatan: Jika kunci API dikosongkan, kalkulator akan beralih secara otomatis ke "Mode Daftar Lokasi" yang aman).*

### Langkah 8: Jalankan Server Lokal
```bash
php artisan serve
```
Akses website melalui peramban pada alamat: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 3. Kredensial Administrator Awal (Default Admin Account)
Setelah database di-seed, Anda dapat masuk ke panel admin di `/admin/login` menggunakan kredensial:
- **URL Login:** `http://127.0.0.1:8000/admin/login`
- **Email:** `admin@balitourservice.local`
- **Password Bawaan:** `AdminJumu2026!`

> [!IMPORTANT]
> **Keamanan:** Segera ubah kredensial administrator saat melakukan deployment ke server production!

---

## 4. Verifikasi Automated Test Suite
Pastikan seluruh fitur berjalan tanpa cela dengan mengeksekusi test suite:
```bash
php artisan test
```
*Hasil yang diharapkan: 108 tests passed (351 assertions).*
