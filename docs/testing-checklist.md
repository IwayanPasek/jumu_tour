# Checklist Pengujian Manual & Verifikasi Kualitas (Quality Assurance)
**Proyek:** Website Layanan Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 13)  
**Tanggal Uji:** 23 September 2026  

---

## 1. Lingkungan Pengujian (Test Environments)
- **Local Dev / Staging:** PHP 8.3.16 / 8.2 Fallback, MariaDB 10.4.32 (XAMPP), Apache 2.4.
- **Target Deployment:** Apache Shared Hosting (cPanel), Linux x86_64, MySQL 8.0 / MariaDB 10.5+, HTTPS Enabled.
- **Cache Driver:** Database / File.
- **Session Driver:** File / Database (`SESSION_SECURE_COOKIE=true` pada HTTPS).

---

## 2. Matriks Browser & Perangkat yang Diuji
- **Desktop:** Google Chrome (v128+), Mozilla Firefox (v129+), Microsoft Edge (v128+), Safari macOS.
- **Mobile Devices:** Android Chrome (Pixel/Samsung viewport: 360x800, 412x915), iOS Safari (iPhone 14/15 viewport: 390x844).

---

## 3. Matriks Skenario Pengujian Manual (Test Cases Matrix)

| ID | Modul / Kategori | Skenario Pengujian | Hasil yang Diharapkan (Expected Result) | Hasil Aktual (Actual Result) | Status | Severity Bug | Catatan |
|---|---|---|---|---|---|---|---|
| **TC-01** | Autentikasi | Login admin dengan email & password valid | Berhasil login, session diregenerasi, diarahkan ke dashboard admin | Sesuai ekspektasi | **PASS** | - | - |
| **TC-02** | Autentikasi | Login admin dengan password salah | Ditolak, pesan kesalahan umum tanpa rincian status akun | Sesuai ekspektasi | **PASS** | - | - |
| **TC-03** | Autentikasi | Brute force login 6x dalam 1 menit | Percobaan ke-6 ditolak oleh rate limiter (HTTP 429 atau pesan tunggu) | Sesuai ekspektasi | **PASS** | Low | Proteksi brute force aktif |
| **TC-04** | Autentikasi | Akses URL dashboard langsung oleh guest | Dialihkan (*redirect*) ke `/admin/login` | Sesuai ekspektasi | **PASS** | - | - |
| **TC-05** | Autentikasi | Logout via tombol POST | Sesi di-invalidate, token CSRF di-refresh, kembali ke halaman login | Sesuai ekspektasi | **PASS** | - | - |
| **TC-06** | Autentikasi | Logout via URL GET manual (`/admin/logout`) | Ditolak dengan HTTP 405 Method Not Allowed | Sesuai ekspektasi | **PASS** | - | - |
| **TC-07** | CRUD Region | Tambah & edit daerah wisata dengan slug otomatis | Tersimpan ke database, slug unik terbentuk, tampil di list | Sesuai ekspektasi | **PASS** | - | - |
| **TC-08** | CRUD Region | Hapus region yang masih memiliki destinasi wisata | Ditolak dengan flash error "masih digunakan oleh destinasi" | Sesuai ekspektasi | **PASS** | - | Integritas foreign key terjaga |
| **TC-09** | CRUD Category | Tambah & ubah status kategori aktif/nonaktif | Status toggle instan via PATCH, kategori nonaktif tersembunyi di publik | Sesuai ekspektasi | **PASS** | - | - |
| **TC-10** | CRUD Destination | Tambah destinasi dengan upload foto valid (JPG/PNG/WEBP < 5MB) | Gambar tersimpan di `storage/app/public/destinations`, nama ter-hash aman | Sesuai ekspektasi | **PASS** | - | - |
| **TC-11** | CRUD Destination | Upload file non-gambar (misal `.pdf` atau `.exe`) | Ditolak oleh validasi Form Request dengan pesan error ramah | Sesuai ekspektasi | **PASS** | Medium | Mitigasi upload berbahaya |
| **TC-12** | CRUD Destination | Input koordinat latitude > 90 atau longitude > 180 | Ditolak dengan validasi range koordinat geografis | Sesuai ekspektasi | **PASS** | - | - |
| **TC-13** | Peta Interaktif | Halaman kalkulator rute dibuka saat Maps Key terpasang | Peta Google Maps interaktif tampil, marker jemput (hijau) & wisata muncul | Sesuai ekspektasi | **PASS** | - | - |
| **TC-14** | Peta Interaktif | Halaman kalkulator rute dibuka tanpa Maps Key | Muncul fallback ramah "Mode Daftar Lokasi", sistem tetap dapat dipakai | Sesuai ekspektasi | **PASS** | Low | Graceful fallback aktif |
| **TC-15** | Rute & Estimasi | Pilih titik jemput + 3 tujuan wisata, klik "Hitung Rute" | Jarak riil jalan raya, durasi, dan garis polyline biru tergambar di peta | Sesuai ekspektasi | **PASS** | - | Server-side Google Routes API |
| **TC-16** | Rute & Estimasi | Urutkan tujuan secara manual (tombol Naik/Turun) | Urutan rute dan nomor urutan marker di peta berubah sesuai aksi user | Sesuai ekspektasi | **PASS** | - | Tanpa auto-optimization |
| **TC-17** | Rute & Estimasi | Coba tambah tujuan ke-6 | Tombol tambah nonaktif atau ditolak dengan batasan maksimal 5 tujuan | Sesuai ekspektasi | **PASS** | - | Kuota MVP dipatuhi |
| **TC-18** | Kalkulasi Tarif | Perhitungan tarif 1 penumpang vs 4 penumpang | Penumpang ke-2 s/d ke-4 dikenakan extra passenger fee sesuai config | Sesuai ekspektasi | **PASS** | - | Server-side calculation |
| **TC-19** | Kalkulasi Tarif | Perjalanan jarak sangat pendek (subtotal < tarif minimum) | Sistem menerapkan tarif minimum (*minimum price applied badge*) | Sesuai ekspektasi | **PASS** | - | - |
| **TC-20** | Kalkulasi Tarif | Pembulatan akhir rupiah | Angka dibulatkan ke kelipatan Rp1.000 terdekat ke atas (*ceil*) | Sesuai ekspektasi | **PASS** | - | Tampilan rapi tanpa pecahan |
| **TC-21** | WhatsApp Booking | Klik tombol reservasi WhatsApp dengan data valid | Menghasilkan tautan resmi `https://wa.me/628xxx` memuat rincian ter-encode | Sesuai ekspektasi | **PASS** | - | Tidak tersimpan di DB |
| **TC-22** | WhatsApp Booking | Pemesanan dengan tanggal kemarin/lampau | Ditolak dengan validasi "Tanggal tour tidak boleh di masa lalu" | Sesuai ekspektasi | **PASS** | - | - |
| **TC-23** | Keamanan XSS | Input nama tujuan wisata dengan `<script>alert(1)</script>` | Ter-escape sempurna pada HTML publik (`&lt;script&gt;`), tidak dieksekusi | Sesuai ekspektasi | **PASS** | High | Blade auto-escaping aktif |
| **TC-24** | Keamanan CSRF | Request POST ke endpoint tanpa token CSRF | Ditolak dengan HTTP 419 Page Expired | Sesuai ekspektasi | **PASS** | High | CSRF middleware aktif |
| **TC-25** | Keamanan Dotfiles | Akses langsung ke `http://domain.local/.env` di web server Apache | Ditolak langsung dengan HTTP 403 Forbidden oleh rule `.htaccess` | Sesuai ekspektasi | **PASS** | Critical | Secret terlindungi |
| **TC-26** | Error Handling | Akses URL acak yang tidak terdaftar | Tampil halaman custom error 404 responsif dengan tombol ke Beranda | Sesuai ekspektasi | **PASS** | - | Bebas dari debug trace |
| **TC-27** | Error Handling | Simulasi kegagalan server internal (HTTP 500) saat `APP_DEBUG=false` | Tampil halaman custom error 500 ramah tanpa bocoran query database | Sesuai ekspektasi | **PASS** | High | Stack trace disembunyikan |
| **TC-28** | Tampilan Mobile | Membuka kalkulator dan katalog wisata pada layar ponsel (360px) | Layout bertumpuk rapi, peta responsif, kontrol sentuh nyaman | Sesuai ekspektasi | **PASS** | - | Mobile-friendly |

---

## 4. Kesimpulan Hasil Uji (Acceptance Summary)
- **Total Skenario Uji:** 28 Skenario
- **Hasil Lolos (Passed):** 28 (100%)
- **Hasil Gagal (Failed):** 0 (0%)
- **Defect Kritis:** 0
- **Status Akhir:** **READY FOR DEPLOYMENT (MEMENUHI SELURUH STANDAR MVP)**
