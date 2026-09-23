# Jumu Bali Tour - MVP

Platform layanan private tour Bali berbasis web dengan perencanaan multi-tujuan, estimasi jarak & durasi via Google Routes API, estimasi tarif transparan, dan pemesanan instan melalui WhatsApp.

---

## 1. Spesifikasi Teknologi

- **Framework**: Laravel 12 (Kompatibel penuh PHP 8.2 dan PHP 8.3)
- **Bahasa**: PHP >= 8.2.0 (Target produksi: PHP 8.3, Fallback: PHP 8.2)
- **Database**: MySQL / MariaDB (Eloquent ORM)
- **Frontend**: Laravel Blade + Bootstrap 5.3 (via CDN) + Bootstrap Icons + Custom CSS
- **Interaksi**: Alpine.js / Vanilla JS (ringan, tanpa kewajiban Node.js runtime di hosting)
- **Integrasi Eksternal**:
  - Google Maps JavaScript API (Tampilan peta interaktif)
  - Google Routes API (Perhitungan jarak & durasi server-to-server)
  - WhatsApp Click-to-Chat (Pemesanan & konfirmasi)
- **Target Hosting**: Apache cPanel Shared Hosting

---

## 2. Panduan Menjalankan di Lingkungan Lokal (Windows / XAMPP)

### Prasyarat
1. **PHP**: Versi 8.2 atau 8.3 dengan ekstensi aktif: `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `openssl`, `xml`, `bcmath`.
2. **Composer**: Versi 2.x terpasang.
3. **MySQL**: Menjalankan MySQL via XAMPP Control Panel (default port 3306).

### Langkah Instalasi Lokal
1. Salin template environment:
   ```bash
   copy .env.example .env
   ```
2. Pastikan file `.env` memiliki konfigurasi database lokal:
   ```env
   APP_NAME="Jumu Bali Tour"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jumu_tour
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Buat database baru di MySQL lokal (melalui phpMyAdmin atau heidiSQL/CLI):
   ```sql
   CREATE DATABASE jumu_tour CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
5. Buka browser pada alamat: [http://localhost:8000](http://localhost:8000)

---

## 3. Panduan Setup Database di Hosting cPanel

Jika Anda bersiap melakukan deployment ke hosting cPanel:

1. **Masuk ke cPanel** akun hosting Anda.
2. Cari menu **MySQL Databases** atau **MySQL Database Wizard**.
3. **Penting Mengenai Prefix**:
   - Di cPanel shared hosting, nama database dan nama pengguna MySQL hampir selalu memiliki **prefix akun cPanel**.
   - Contoh: Jika username login cPanel Anda adalah `jumutour`, nama database yang dibuat adalah `jumutour_db` dan usernamenya `jumutour_user`.
4. **Langkah Pembuatan**:
   - Buat database: masukkan nama (misal: `db`). Hasilnya: `prefix_db`.
   - Buat user database: masukkan nama user dan generate password kuat. Hasilnya: `prefix_user`.
   - Tambahkan user ke database (*Add User To Database*) dan centang opsi **ALL PRIVILEGES**.
5. Salin detail tersebut ke `.env` server hosting Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=prefix_db
   DB_USERNAME=prefix_user
   DB_PASSWORD=PasswordKuatAnda
   ```

---

## 4. Panduan Persiapan Deployment cPanel (Apache)

Karena shared hosting umumnya tidak memiliki Node.js, Composer, atau SSH terbuka:

### Opsi Struktur Folder di cPanel
Di cPanel, dokumen root domain biasanya diarahkan ke folder `public_html/`.

1. **Struktur Direktori Aman**:
   - Letakkan seluruh file Laravel (app, bootstrap, config, database, routes, vendor, storage, .env) ke dalam satu folder di luar `public_html/`, misalnya di `/home/username/laravel_app/`.
   - Pindahkan seluruh isi folder `public/` ke dalam `/home/username/public_html/`.
   - Buka `/home/username/public_html/index.php`, sesuaikan baris autoloader dan bootstrap:
     ```php
     require __DIR__.'/../laravel_app/vendor/autoload.php';
     $app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';
     ```
2. **Ketiadaan Node.js di cPanel**:
   - Desain frontend proyek ini menggunakan **Bootstrap 5.3 CDN** dan CSS kustom statis di `public/assets/css/custom.css`.
   - Anda **tidak perlu** menjalankan `npm install` atau `npm run build` di server cPanel.
3. **Ketiadaan Composer di cPanel**:
   - Jalankan `composer install --optimize-autoloader --no-dev` di komputer lokal.
   - Unggah folder `vendor/` bersama file proyek ke server cPanel dalam bentuk file zip lalu ekstrak melalui File Manager cPanel.
4. **Versi PHP**:
   - Buka menu **MultiPHP Manager** di cPanel dan pastikan domain Anda diset menggunakan PHP 8.2 atau PHP 8.3.

---

## 5. Status Pengerjaan (Cluster Status)

- [x] **Cluster 1**: Fondasi Project, Konfigurasi Dasar, Struktur Modular MVC, Layout Publik, dan Halaman Beranda Sementara.
- [ ] **Cluster 2**: Desain Database, Migration, dan Seeder (Districts, Categories, Destinations, Pricing, Brand Settings).
- [ ] **Cluster 3**: Modul Autentikasi Admin, Dashboard, dan CRUD Master Data (Daerah & Kategori).
- [ ] **Cluster 4**: CRUD Tempat Wisata / Destinasi & Upload Gambar.
- [ ] **Cluster 5**: Katalog Publik Tempat Wisata & Filter Pencarian.
- [ ] **Cluster 6**: Integrasi Google Maps & Perhitungan Rute (Google Routes API).
- [ ] **Cluster 7**: Kalkulator Tarif & Estimasi Biaya Perjalanan.
- [ ] **Cluster 8**: Form Pemesanan, Integrasi Template Pesan WhatsApp, & Pengaturan Brand.
