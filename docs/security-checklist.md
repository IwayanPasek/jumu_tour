# Checklist Audit Keamanan Sistem (OWASP ASVS Verification)
**Proyek:** Website Layanan Private Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 17 Final Audit)  
**Standar Evaluasi:** OWASP Application Security Verification Standard (ASVS v4.0) Level 1/2 Baseline  
**Tanggal Audit:** 23 September 2026  
**Status Evaluasi:** **100% COMPLIANT / PASS (Zero High-Risk Vulnerabilities)**  

---

## 1. Matriks Audit Keamanan Kategori OWASP ASVS

| Kategori OWASP ASVS | Item Verifikasi & Kontrol Keamanan | Status | Penjelasan Teknis & Implementasi Kode |
|---|---|---|---|
| **V1: Architecture & Design** | Pemisahan hak akses admin & publik | **PASS** | Rute `/admin/*` dilindungi middleware terpisah `admin` (`EnsureAdminAuthenticated`). Rute publik bebas dari fungsionalitas destruktif. |
| **V2: Authentication** | Proteksi brute-force login | **PASS** | Endpoint `/admin/login` dilindungi rate limiting `throttle:5,1`. Percobaan ke-6 ditolak otomatis. |
| **V2: Authentication** | Hashing password terstandar | **PASS** | Password admin di-hash menggunakan algoritma Bcrypt (`BCRYPT_ROUNDS=12`) tanpa penyimpanan plaintext. |
| **V3: Session Management** | Regenerasi ID sesi setelah login | **PASS** | Method login memanggil `$request->session()->regenerate()` untuk mencegah serangan *Session Fixation*. |
| **V3: Session Management** | Konfigurasi cookie aman | **PASS** | `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, dan `SESSION_SAME_SITE=lax` aktif di production. |
| **V3: Session Management** | Invalidation sesi pada logout | **PASS** | Method logout memanggil `$request->session()->invalidate()` dan `$request->session()->regenerateToken()`. Logout wajib menggunakan method HTTP POST. |
| **V4: Access Control** | Proteksi akses dashboard admin | **PASS** | Pengguna guest atau user non-admin yang mencoba mengakses `/admin/dashboard` langsung dialihkan ke `/admin/login`. |
| **V4: Access Control** | Mencegah Insecure Direct Object References (IDOR) | **PASS** | Akses manipulasi data dibatasi ke admin yang terotentikasi. Destinasi nonaktif tersembunyi otomatis dari katalog publik. |
| **V5: Validation & Encoding** | Perlindungan Cross-Site Scripting (XSS) | **PASS** | Blade templating meng-escape seluruh output data secara otomatis (`{{ $variable }}`). Tag HTML dan script dieksekusi sebagai teks biasa. |
| **V5: Validation & Encoding** | Validasi input form request | **PASS** | Setiap form (tambah/edit destinasi, daerah, kategori, kalkulator rute, WhatsApp) memiliki kelas `FormRequest` khusus dengan aturan validasi ketat. |
| **V5: Validation & Encoding** | Pencegahan SQL Injection | **PASS** | Seluruh interaksi database menggunakan Eloquent ORM dan PDO prepared statements berparameter. Tidak ada string concatenation SQL mentah. |
| **V6: Malicious File Handling** | Validasi berkas unggahan gambar | **PASS** | Upload foto tempat wisata dibatasi pada tipe MIME: `jpeg`, `png`, `jpg`, `webp` dengan ukuran maksimal 5 MB (`StoreDestinationRequest`). |
| **V6: Malicious File Handling** | Penolakan file SVG dan executable | **PASS** | File `.svg` ditolak untuk mencegah SVG XSS attack. File `.php`, `.phtml`, `.sh`, `.exe` ditolak secara mutlak. |
| **V6: Malicious File Handling** | Proteksi direktori penyimpanan upload | **PASS** | Nama file di-hash secara acak (`Storage::disk('public')->putFile(...)`). Rule `.htaccess` pada web server mencegah eksekusi skrip dari folder storage. |
| **V7: Error Handling & Logging** | Penyembunyian stack trace production | **PASS** | Diatur `APP_DEBUG=false` di server production. Halaman kustom error HTTP 403, 404, 419, 429, dan 500 ramah pengguna tanpa kebocoran kode/kueri. |
| **V7: Error Handling & Logging** | Sanitasi data pada log sistem | **PASS** | `LOG_LEVEL=error` di production. File log tidak mencatat password, token otentikasi, maupun API Key privat. |
| **V8: Data Protection** | Keamanan file konfigurasi `.env` | **PASS** | File `.env` diletakkan di luar document root web server (`/home/USER/tour-bali-app/.env`). Direktif Apache `.htaccess` memblokir akses ke berkas tersembunyi (*dotfiles*). |
| **V8: Data Protection** | Privasi data pemesan (Privacy by Design) | **PASS** | Data reservasi WhatsApp tidak disimpan ke database (*stateless booking*). Tidak ada risiko kebocoran basis data pelanggan di hosting. |
| **V9: Communication Security** | Penegakan protokol HTTPS | **PASS** | `APP_URL` menggunakan HTTPS. Fitur *Force HTTPS Redirect* diaktifkan di cPanel. Cookie sesi ditandai `secure`. |
| **V10: Malicious Logic & Tampering** | Perlindungan Cross-Site Request Forgery (CSRF) | **PASS** | Seluruh form POST, PUT, PATCH, dan DELETE dilindungi token CSRF `@csrf`. Permintaan tanpa token valid ditolak HTTP 419. |
| **V10: Malicious Logic** | Pencegahan manipulasi tarif di client | **PASS** | Nilai jarak, durasi, dan estimasi biaya dihitung ulang 100% di sisi server. Backend tidak mempercayai data nominal atau jarak yang dikirim oleh peramban. |
| **V11: Business Logic** | Pembatasan kuota tujuan wisata | **PASS** | Kalkulator membatasi pemilihan destinasi antara 1 hingga maksimal 5 tujuan untuk mencegah penyalahgunaan kuota Google Routes API. |
| **V11: Business Logic** | Validasi tanggal tour di masa depan | **PASS** | Validasi `date|after_or_equal:today` memastikan pemesanan tidak dilakukan pada tanggal yang telah lampau. |
| **V12: Files & Resources** | Perlindungan integritas referensial data | **PASS** | Foreign key constraints menggunakan `restrictOnDelete` pada relasi daerah-destinasi. Daerah yang masih memiliki tempat wisata tidak dapat dihapus. |
| **V13: API Security** | Pemisahan kunci API Google Maps | **PASS** | Browser Key hanya diizinkan untuk domain terdaftar via HTTP Referrer. Server Key Google Routes disimpan privat di `.env` server. |
| **V13: API Security** | Pembatasan field mask API eksternal | **PASS** | Request Routes API menyertakan header `X-Goog-FieldMask` spesifik (bukan wildcard `*`), menghemat resource dan memperketat permukaan data. |
| **V14: Configuration** | Security Headers HTTP | **PASS** | Middleware `SecurityHeadersMiddleware` menginjeksi header: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`. |

---

## 2. Kesimpulan Audit Keamanan
Sistem memenuhi seluruh kriteria keamanan aplikasi web modern (OWASP ASVS Baseline) dan dinyatakan **AMAN** untuk beroperasi di lingkungan produksi shared hosting cPanel.
