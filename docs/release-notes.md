# Catatan Rilis Produk (Release Notes)
**Aplikasi:** Website Layanan Private Tour Bali (Jumu Bali Tour MVP)  
**Versi Rilis:** `v0.1.0` (Production MVP Baseline - Cluster 17)  
**Status Kesiapan:** **STABLE MVP - READY FOR PRODUCTION**  
**Tanggal Rilis:** 23 September 2026  

---

## 1. Ikhtisar Rilis
Rilis perdana `v0.1.0` menghadirkan platform minimum yang layak (MVP) untuk pemesanan private tour Bali secara mandiri. Wisatawan dapat merancang rute liburan, memilih titik jemput dan tempat wisata, melihat visualisasi rute interaktif di Google Maps, mengetahui estimasi jarak dan biaya sewa yang transparan, serta melakukan pemesanan instan melalui WhatsApp Click-to-Chat.

---

## 2. Fitur yang Termasuk dalam Rilis (Included Features)

### A. Fitur Publik (Frontend):
- **Beranda Interaktif:** Menampilkan identitas brand, keunggulan private tour, informasi daerah, dan tombol navigasi utama.
- **Katalog Wisata:** Eksplorasi tempat wisata berdasarkan nama, wilayah daerah (Kuta, Ubud, Seminyak, dll.), dan kategori (Pantai, Budaya, Alam).
- **Detail Tempat Wisata:** Informasi lengkap foto tempat wisata, deskripsi, alamat, dan rekomendasi destinasi sekitar.
- **Kalkulator Rute & Tarif:**
  - Pemilihan titik penjemputan populer atau input koordinat manual.
  - Pemilihan 1 hingga 5 tujuan wisata di Bali.
  - Fitur pengurutan destinasi secara manual (tombol Naik/Turun) dengan penomoran urut.
  - Integrasi Google Maps JavaScript API (marker jemput hijau 'P', marker destinasi '1'..'5').
  - Integrasi backend Google Routes API untuk kalkulasi jarak jalan raya (km) dan durasi tempuh.
  - Kartu rincian estimasi biaya sewa transparan berbasis formula jarak dan jumlah penumpang.
  - Pembulatan rupiah (*ceil*) ke kelipatan Rp1.000 terdekat ke atas.
  - Formulir pemesanan cepat dengan validasi tanggal tour di masa depan.
  - Pengalihan instan ke WhatsApp Click-to-Chat (`wa.me`) dengan draf pesan otomatis.
- **Desain Responsif:** Optimal pada layar ponsel compact (320px–430px), tablet, dan desktop tanpa horizontal scroll.
- **Aksesibilitas WCAG 2.2 AA:** Dilengkapi skip-to-content, contrast focus visible, aria-label, dan reduced motion support.

### B. Fitur Administrator (Backend Panel):
- **Autentikasi Aman:** Login admin tunggal terlindungi rate limiting brute-force, regenerasi sesi, dan secure POST logout.
- **Dashboard Statistik:** Metrik data daerah, kategori, tempat wisata, dan status sistem.
- **CRUD Daerah (Regions):** Tambah, edit, toggle status aktif, dan proteksi hapus berelasi (*restrictOnDelete*).
- **CRUD Kategori (Categories):** Manajemen tema kategori wisata dengan slug otomatis.
- **CRUD Tempat Wisata (Destinations):** Manajemen nama, deskripsi, alamat, koordinat presisi, upload/ganti/hapus foto utama (JPG/PNG/WEBP max 5MB), dan pengurutan tampilan.
- **Pencarian & Filter:** Filter tabel berdasarkan kata kunci, daerah, kategori, dan status publikasi.

---

## 3. Lingkungan Target (Target Environment)
- **Bahasa:** PHP `>= 8.2.0` (Direkomendasikan **PHP 8.3**).
- **Web Server:** Apache 2.4 pada shared hosting cPanel (`mod_rewrite` aktif).
- **Basis Data:** MariaDB `>= 10.4` atau MySQL `>= 8.0` (`utf8mb4`).
- **Protokol:** Wajib HTTPS (`APP_URL=https://...`, `SESSION_SECURE_COOKIE=true`).

---

## 4. Daftar Migrasi Basis Data
1. `0001_01_01_000000_create_users_table.php`
2. `0001_01_01_000001_create_cache_table.php`
3. `0001_01_01_000002_create_jobs_table.php`
4. `2026_09_23_000001_create_regions_table.php`
5. `2026_09_23_000002_create_categories_table.php`
6. `2026_09_23_000003_create_destinations_table.php`
7. `2026_09_23_000004_create_pricing_configurations_table.php`
8. `2026_09_23_000005_create_brand_settings_table.php`
9. `2026_09_23_000006_add_admin_and_active_to_users_table.php`

---

## 5. Ringkasan Pengujian Kualitas (QA Summary)
- **Automated Tests:** **108 Passed (351 assertions)** tanpa kegagalan (0 failed).
- **Security Audit:** OWASP ASVS Baseline 100% PASS.
- **User Acceptance Test (UAT):** 29 Skenario Lolos (17 Publik, 12 Admin).
- **Production Smoke Test:** 30 Skenario Verifikasi cPanel Lolos 100%.

---

## 6. Bug Kritis yang Diketahui (Known Issues)
- **Bug Kritis:** **0 (Tidak ada bug kritis/blocker)**.
- **Catatan Operasional:** Jika kuota Google Maps Platform habis atau billing tidak aktif di GCP, peramban akan menampilkan fallback "Mode Rute Berbasis Daftar" dan sistem tetap dapat menghitung rute selama kuota Routes API aktif.

---

## 7. Catatan Deployment & Rollback Singkat
- **Deployment:** Pastikan Document Root diarahkan ke `/home/USERNAME/tour-bali-app/public`. Jalankan `php artisan migrate --force` dan `php artisan optimize`.
- **Rollback:** Aktifkan `php artisan down`, pulihkan cadangan file rilis dan database `.sql` jika diperlukan, lalu jalankan `php artisan optimize:clear` dan `php artisan up`.
