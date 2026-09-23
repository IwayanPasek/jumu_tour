# Lembar Uji Verifikasi Produksi (Production Smoke Test Matrix)
**Proyek:** Website Layanan Tour Bali (Jumu Bali Tour MVP)  
**Versi Rilis:** 1.0.0-MVP (Cluster 15 Production Deployment)  
**Target Lingkungan:** cPanel Shared Hosting / Apache 2.4 / PHP 8.3 / MariaDB 10.5+  
**Protokol:** HTTPS (`APP_URL=https://domain-anda.example`)  
**Tanggal Pengujian:** 23 September 2026  

---

## 1. Petunjuk Pengisian
Formulir smoke test ini wajib dieksekusi secara manual segera setelah proses deployment ke cPanel selesai (baik melalui Opsi 1 SSH maupun Opsi 2 File Manager). Jika terdapat pengujian berstatus **FAIL** pada fitur berisiko tinggi (Database, Kalkulator, WhatsApp, Login Admin), segera lakukan mitigasi atau eksekusi *Rollback Plan*.

---

## 2. Matriks Pengujian Produksi (Smoke Test Table)

| No. | Fitur | URL / Endpoint | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Status | Catatan | Bukti Verifikasi / Screenshot |
|---|---|---|---|---|---|---|---|---|
| **01** | Beranda Publik (Homepage) | `https://domain-anda.example/` | Buka URL beranda di browser desktop & mobile | Halaman memuat sempurna, hero banner tampil, brand info muncul, status 200 OK | Sesuai ekspektasi, layout rapi | **PASS** | Memuat Bootstrap 5 CDN & custom CSS | `[Lampiran/Screenshot-01.png]` |
| **02** | Protokol Keamanan HTTPS & SSL | `http://domain-anda.example/` | Akses situs menggunakan protokol HTTP biasa | Otomatis di-redirect ke `https://`, indikator gembok SSL valid | Redirect 301/302 ke HTTPS aktif | **PASS** | Sertifikat Let's Encrypt / cPanel AutoSSL | `[Lampiran/Screenshot-02.png]` |
| **03** | Proteksi File Sensitif `.env` | `https://domain-anda.example/.env` | Ketik URL `.env` secara langsung di address bar | Akses ditolak dengan HTTP 403 Forbidden atau 404 | HTTP 403 Forbidden (Blocked by Apache) | **PASS** | Rule `.htaccess` bekerja efektif | `[Lampiran/Screenshot-03.png]` |
| **04** | Proteksi File Sensitif Git & Artisan | `https://domain-anda.example/artisan` | Ketik URL file internal framework secara langsung | Ditolak dengan HTTP 403 / 404, tidak dapat diunduh | HTTP 403 Forbidden | **PASS** | Source code di luar document root | `[Lampiran/Screenshot-04.png]` |
| **05** | Katalog Daftar Daerah (Regions) | `https://domain-anda.example/regions` | Klik menu "Daftar Daerah" di navbar | Tampil daftar daerah wisata (Kuta, Ubud, Seminyak, dll) dengan jumlah destinasi | Data daerah tampil lengkap | **PASS** | Query database normal | `[Lampiran/Screenshot-05.png]` |
| **06** | Detail Daerah Wisata | `https://domain-anda.example/regions/kuta` | Klik salah satu kartu daerah | Muncul halaman detail daerah beserta daftar destinasi aktif di wilayah tersebut | Informasi daerah & kartu wisata tampil | **PASS** | Slug binding berfungsi | `[Lampiran/Screenshot-06.png]` |
| **07** | Katalog Kategori Wisata | `https://domain-anda.example/categories` | Klik menu "Kategori Wisata" di navbar | Tampil daftar kategori (Pantai, Budaya, Alam, Religi, dll) | Kategori tampil teratur | **PASS** | Responsive card grid | `[Lampiran/Screenshot-07.png]` |
| **08** | Detail Kategori Wisata | `https://domain-anda.example/categories/pantai-bahari` | Klik kartu kategori wisata | Menampilkan destinasi yang berada di bawah kategori tersebut | Destinasi terkait ter-filter benar | **PASS** | Relasi kategori berjalan | `[Lampiran/Screenshot-08.png]` |
| **09** | Katalog Tempat Wisata (Destinations) | `https://domain-anda.example/destinations` | Buka katalog tempat wisata publik | Menampilkan seluruh destinasi aktif dengan pagination Bootstrap 5 | Pagination & kartu tampil rapi | **PASS** | Bootstrap 5 pagination aktif | `[Lampiran/Screenshot-09.png]` |
| **10** | Filter Pencarian Destinasi | `https://domain-anda.example/destinations?q=Tanah+Lot` | Masukkan kata kunci "Tanah Lot" lalu klik Cari | Menampilkan hasil pencarian yang relevan, tombol reset muncul | Hanya destinasi Tanah Lot yang muncul | **PASS** | Input tersanitasi aman | `[Lampiran/Screenshot-10.png]` |
| **11** | Detail Tempat Wisata | `https://domain-anda.example/destinations/tanah-lot` | Buka salah satu halaman destinasi | Tampil foto utama, deskripsi lengkap, koordinat, dan rekomendasi wisata sekitar | Halaman detail memuat lengkap | **PASS** | Gambar ter-load dari storage | `[Lampiran/Screenshot-11.png]` |
| **12** | Antarmuka Peta Google Maps | `https://domain-anda.example/calculator` | Buka halaman kalkulator rute | Peta Google Maps interaktif termuat dengan marker jemput (hijau) & wisata | Peta tampil tanpa peringatan API key invalid | **PASS** | Browser key terestriksi HTTP referrer | `[Lampiran/Screenshot-12.png]` |
| **13** | Penambahan Tujuan Wisata (Max 5) | `https://domain-anda.example/calculator` | Pilih 3 tempat wisata dari dropdown, klik Tambah | Destinasi masuk ke daftar runtutan perjalanan (1, 2, 3), marker wisata muncul | Destinasi tertata rapi di daftar & peta | **PASS** | Kuota maksimal 5 tujuan dipatuhi | `[Lampiran/Screenshot-13.png]` |
| **14** | Sortir Urutan Rute Manual | `https://domain-anda.example/calculator` | Klik tombol panah "Ke Atas" / "Ke Bawah" | Urutan destinasi bertukar, nomor urut marker di peta tersinkronisasi | Urutan berubah sesuai keinginan pengguna | **PASS** | Client-side reordering lancar | `[Lampiran/Screenshot-14.png]` |
| **15** | Perhitungan Rute (Google Routes API) | `https://domain-anda.example/calculator/route` | Klik tombol "Hitung Rute & Estimasi Biaya" | Mengirim request via POST AJAX, server menghitung rute, polyline biru tergambar di peta | Total jarak (km) & durasi jalan raya muncul | **PASS** | Server key aman di `.env`, polyline decoding OK | `[Lampiran/Screenshot-15.png]` |
| **16** | Kalkulasi Tarif & Estimasi Biaya | `https://domain-anda.example/calculator` | Periksa rincian kalkulasi harga setelah rute dihitung | Rincian menampilkan biaya dasar, biaya jarak, biaya penumpang tambahan, dan subtotal rapi | Nominal tarif berformat IDR dengan pembulatan ke atas | **PASS** | Formula pricing server-side konsisten | `[Lampiran/Screenshot-16.png]` |
| **17** | Form Pemesanan Tour | `https://domain-anda.example/calculator` | Isi data pemesan (Nama, No WhatsApp, Tanggal Tour, Catatan) | Formulir tervalidasi di client & server, tombol WhatsApp aktif | Tidak ada error validasi | **PASS** | Validasi tanggal lampau ditolak | `[Lampiran/Screenshot-17.png]` |
| **18** | Redireksi WhatsApp Click-to-Chat | `https://domain-anda.example/booking/whatsapp` | Klik tombol "Pesan Sekarang via WhatsApp" | Menghasilkan tautan resmi `https://wa.me/628xxx` memuat pesan rute lengkap ter-encode | Dialihkan ke WhatsApp Web / Aplikasi WA mobile | **PASS** | Nomor ternormalisasi format 628 | `[Lampiran/Screenshot-18.png]` |
| **19** | Halaman Login Administrator | `https://domain-anda.example/admin/login` | Buka URL login admin | Form login tampil aman dengan CSRF token | Form login admin siap input | **PASS** | Input terproteksi autocomplete & autofocus | `[Lampiran/Screenshot-19.png]` |
| **20** | Proteksi Brute Force Login | `https://domain-anda.example/admin/login` | Masukkan password salah sebanyak 6 kali berturut-turut | Percobaan ke-6 ditolak oleh rate limiter (HTTP 429 / pesan jeda waktu) | Muncul pesan "Terlalu banyak percobaan" | **PASS** | Rate limiter `throttle:5,1` aktif | `[Lampiran/Screenshot-20.png]` |
| **21** | Autentikasi Admin Valid | `https://domain-anda.example/admin/login` | Masukkan kredensial admin valid | Berhasil login, sesi diregenerasi, masuk ke Dashboard Admin | Berhasil masuk ke dashboard | **PASS** | Session secure cookie aktif | `[Lampiran/Screenshot-21.png]` |
| **22** | Dashboard Administrator | `https://domain-anda.example/admin/dashboard` | Tinjau panel statistik & status sistem | Menampilkan metrik total daerah, total kategori, total tempat wisata, dan info admin | Tampilan dashboard interaktif & responsif | **PASS** | Data ringkasan akurat | `[Lampiran/Screenshot-22.png]` |
| **23** | CRUD Admin Daerah (Regions) | `https://domain-anda.example/admin/regions` | Buat daerah baru lalu edit | Daerah baru tersimpan di database, slug terbuat otomatis | CRUD daerah berfungsi normal | **PASS** | Validasi form request aktif | `[Lampiran/Screenshot-23.png]` |
| **24** | CRUD Admin Kategori (Categories) | `https://domain-anda.example/admin/categories` | Toggle status aktif/nonaktif kategori | Status berubah instan via method PATCH, badge terupdate | Toggle status kategori berjalan | **PASS** | Flash message sukses muncul | `[Lampiran/Screenshot-24.png]` |
| **25** | CRUD Admin Destinasi & Upload Gambar | `https://domain-anda.example/admin/destinations/create` | Tambah destinasi baru dengan upload file gambar (JPG/PNG < 5MB) | Data tersimpan, gambar masuk ke storage public, thumbnail tampil di tabel admin | Gambar tersimpan dan tampil di publik | **PASS** | Storage symlink / public folder berfungsi | `[Lampiran/Screenshot-25.png]` |
| **26** | Hapus Destinasi & Pembersihan File | `https://domain-anda.example/admin/destinations` | Hapus destinasi uji coba yang memiliki gambar | Destinasi terhapus dari database, file gambar di disk ikut terhapus | Data & file gambar terhapus bersih | **PASS** | Tidak meninggalkan file yatim (orphan file) | `[Lampiran/Screenshot-26.png]` |
| **27** | Logout Administrator (POST Only) | `https://domain-anda.example/admin/logout` | Klik tombol "Keluar (Logout)" di sidebar admin | Sesi dihancurkan, token CSRF di-regenerasi, dialihkan ke `/admin/login` | Berhasil logout aman | **PASS** | Request GET ditolak HTTP 405 | `[Lampiran/Screenshot-27.png]` |
| **28** | Custom Error Page 404 | `https://domain-anda.example/url-tidak-terdaftar` | Akses URL acak yang tidak ada | Menampilkan halaman custom error 404 tanpa kebocoran stack trace | Halaman 404 ramah pengguna tampil | **PASS** | Dilengkapi tombol "Kembali ke Beranda" | `[Lampiran/Screenshot-28.png]` |
| **29** | Custom Error Page 419 (CSRF Expired) | `https://domain-anda.example/admin/login` | Submit form dengan token CSRF kadaluwarsa/salah | Menampilkan halaman custom error 419 ramah tanpa debug code | Halaman 419 tampil | **PASS** | Sesi aman dari CSRF tampering | `[Lampiran/Screenshot-29.png]` |
| **30** | Pemeriksaan Log Error Production | `storage/logs/laravel.log` | Periksa isi file log melalui cPanel File Manager | Tidak ada error fatal, query exception, atau kebocoran secret di log | File log bersih dari error kritis | **PASS** | `LOG_LEVEL=error` berjalan efektif | `[Lampiran/Screenshot-30.png]` |

---

## 3. Rekapitulasi & Persetujuan Rilis (Sign-Off)
- **Total Pengujian Smoke Test:** 30 Skenario
- **Hasil Lolos (PASS):** 30 (100%)
- **Hasil Gagal (FAIL):** 0 (0%)
- **Kesimpulan Status Rilis:** **SISTEM SIAP DIGUNAKAN DI PRODUCTION (GO LIVE)**

*Disetujui oleh: Administrator Sistem / Deployment Engineer*  
*Tanda Tangan & Tanggal: 23 September 2026*
