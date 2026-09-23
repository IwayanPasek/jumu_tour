# Checklist Pengujian Responsivitas & Aksesibilitas (Responsive & Accessibility QA)
**Proyek:** Website Layanan Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 14)  
**Standar Acuan:** WCAG 2.2 Level AA, Google Mobile-Friendly Guidelines, Bootstrap 5.3 Framework  
**Tanggal Verifikasi:** 23 September 2026  

---

## 1. Ringkasan Eksekutif & Ruang Lingkup
Cluster 14 berfokus pada penyempurnaan menyeluruh pengalaman pengguna (UI/UX) lintas perangkat (khususnya ponsel pintar compact 320px hingga 430px, tablet 768px, dan desktop 1366px+), konsistensi styling antarmuka publik dan panel admin, kepatuhan aksesibilitas dasar (WCAG 2.2 AA), serta integrasi pagination Bootstrap 5 tanpa horizontal overflow.

Seluruh pengujian dilakukan dengan prinsip:
1. **Zero Regresion:** Tidak mengubah skema basis data, logika bisnis, integrasi Google Routes API, autentikasi, ataupun struktur pesan pemesanan WhatsApp.
2. **Framework Consistency:** Menggunakan Bootstrap 5.3 CDN dan custom CSS terpusat (`public/assets/css/custom.css`), tanpa menambahkan library UI berat atau beralih framework.
3. **No Horizontal Scroll:** Menjamin tidak ada elemen yang meluap (*overflow*) melewati batas layar viewport terkecil (320px).

---

## 2. Matriks Uji Viewport & Resolusi Perangkat

| Kategori Viewport | Ukuran Lebar (Width) | Perangkat Acuan / Emulasi | Hasil Uji Layout | Bebas Horizontal Scroll | Status |
|---|---|---|---|---|---|
| **Mobile Compact** | **320px** | iPhone SE (Gen 1), Smartwatch/Compact Display | Kontrol form bertumpuk satu kolom, tombol ramah jempol | **YA** (`overflow-x: clip`) | **PASS** |
| **Mobile Standard** | **360px – 390px** | Galaxy S-series, Pixel, iPhone 12/13/14/15 | Grid 1 kolom, kartu destinasi proporsional, peta tinggi 360px | **YA** | **PASS** |
| **Mobile Large** | **412px – 430px** | Galaxy Note/Ultra, iPhone 14/15 Pro Max | Spasi rapi, navigasi responsif, form booking nyaman | **YA** | **PASS** |
| **Tablet / Fold** | **768px – 991px** | iPad Mini/Air, Surface Duo | Grid 2 kolom, header collapse fleksibel, tabel berkontainer geser | **YA** | **PASS** |
| **Desktop / Laptop** | **1200px – 1920px** | Laptop 13-15", Full HD Desktop | Layout 2 kolom side-by-side (kalkulator & peta sticky), sidebar admin penuh | **YA** | **PASS** |

---

## 3. Matriks Skenario Pengujian Responsivitas (Responsive QA Matrix)

| ID | Komponen / Modul | Skenario Pengujian | Hasil yang Diharapkan (Expected Result) | Hasil Aktual | Status |
|---|---|---|---|---|---|
| **RESP-01** | Layout Publik (`app.blade.php`) | Mengakses navbar publik pada viewport 360px | Menu navigasi menciut (*collapse*) rapi ke dalam hamburger toggler, tidak merusak lebar halaman | Navigasi responsif dan toggler berfungsi lancar | **PASS** |
| **RESP-02** | Layout Publik (`footer.blade.php`) | Membaca footer dan mengklik tautan pada ponsel | Kolom informasi bertumpuk teratur, tautan "Kalkulator Rute & Tarif" aktif dan dapat diklik | Tautan aktif ke `/kalkulator`, touch target luas | **PASS** |
| **RESP-03** | Layout Admin (`admin.blade.php`) | Membuka panel administrator pada layar 375px | Sidebar beralih ke mobile header dengan tombol toggle menu dropdown/collapse, tidak menutupi konten utama | Header kompak dengan tombol toggle collapsible | **PASS** |
| **RESP-04** | Kalkulator Rute (`calculator/index.blade.php`) | Scrolling ke bawah pada mobile (360px) saat melihat formulir | Peta tidak menutupi seluruh layar; `sticky-lg-top` hanya aktif di desktop (>=992px), tinggi peta 360px di ponsel | Scrolling halaman leluasa dan proporsional | **PASS** |
| **RESP-05** | Kalkulator Rute (`calculator/index.blade.php`) | Menambahkan dan menyortir destinasi di layar sentuh | Tombol panah Atas/Bawah dan tombol Hapus memiliki target sentuh minimal 44x44px | Tombol mudah ditekan tanpa salah sentuh | **PASS** |
| **RESP-06** | Form Pemesanan WhatsApp | Mengisi formulir reservasi pada viewport 320px | Input tanggal, nama, email, catatan bertumpuk rapi tanpa teks label yang terpotong | Input 100% responsif dalam batas kontainer | **PASS** |
| **RESP-07** | Katalog Destinasi (`destinations/index.blade.php`) | Membuka halaman daftar destinasi di mobile | Filter pencarian, dropdown daerah & kategori bertumpuk rapi, kartu destinasi proporsional | Grid 1 kolom di ponsel, 3 kolom di desktop | **PASS** |
| **RESP-08** | Tabel Data Admin (Destinasi, Daerah, Kategori) | Melihat daftar tabel admin di viewport 400px | Tabel terbungkus `.table-responsive`, dapat digeser halus secara horizontal di dalam kontainernya tanpa merusak layout luar | Tidak terjadi overflow pada seluruh layar | **PASS** |
| **RESP-09** | Pagination Publik & Admin | Navigasi halaman 1, 2, 3 pada mobile | Pagination Bootstrap 5 terpusat (*centered*), angka halaman tidak keluar layar (*flex-wrap*) | Pagination rapi menggunakan format Bootstrap 5 | **PASS** |
| **RESP-10** | Media & Gambar Destinasi | Menampilkan foto tempat wisata di berbagai resolusi | Gambar mempertahankan rasio aspek (`object-fit: cover`), tidak gepeng, dan memuat `loading="lazy"` | Visual tajam dan performa hemat kuota data | **PASS** |

---

## 4. Matriks Skenario Pengujian Aksesibilitas (WCAG 2.2 AA Matrix)

| ID | Kriteria WCAG 2.2 | Skenario Pengujian | Hasil yang Diharapkan (Expected Result) | Hasil Aktual | Status |
|---|---|---|---|---|---|
| **A11Y-01** | **Bypass Blocks (SC 2.4.1)** | Pengguna navigasi keyboard menekan tombol `[Tab]` pertama kali saat halaman terbuka | Muncul tautan lompat "*Lewati ke Konten Utama*" yang mengarahkan fokus langsung ke elemen `<main>` | Skip link muncul di posisi atas dan berfungsi | **PASS** |
| **A11Y-02** | **Name, Role, Value (SC 4.1.2)** | Pembaca layar (*screen reader*) membaca tombol icon-only di tabel admin (Lihat, Edit, Hapus, Status) | Tombol memiliki atribut `aria-label` deskriptif (misal: "*Lihat detail destinasi Pantai Kuta*") | Screen reader membaca nama aksi dan konteks | **PASS** |
| **A11Y-03** | **Name, Role, Value (SC 4.1.2)** | Pembaca layar membaca tombol pengurutan rute (Naik, Turun, Hapus) pada kalkulator | Tombol memiliki `aria-label="Pindahkan tujuan 1 ke atas"` dsb | Aksesibilitas kontrol rute terbaca jelas | **PASS** |
| **A11Y-04** | **Focus Visible (SC 2.4.7)** | Navigasi menggunakan keyboard pada tautan, tombol, dan input form | Indikator fokus terlihat kontras dengan outline 2px dan box-shadow tanpa merusak estetika mouse user | Status `:focus-visible` aktif dan jelas | **PASS** |
| **A11Y-05** | **Info and Relationships (SC 1.3.1)** | Memeriksa label pada seluruh elemen formulir dan filter | Setiap input memiliki elemen `<label for="...">` atau atribut `aria-label` yang sesuai | Tidak ada input yatim tanpa penanda | **PASS** |
| **A11Y-06** | **Target Size (Minimum) (SC 2.5.8)** | Pengujian target sentuh pada tombol navigasi dan aksi | Luas sentuh tombol aksi minimal 44x44 piksel atau memiliki padding yang memadai | Nyaman disentuh pada perangkat layar sentuh | **PASS** |
| **A11Y-07** | **Animation from Interactions (SC 2.3.3)** | Pengguna mengaktifkan setelan sistem operasi `prefers-reduced-motion: reduce` | Animasi perputaran spinner dan transisi tombol dinonaktifkan atau diminimalkan | Media query CSS mendeteksi dan mereduksi animasi | **PASS** |
| **A11Y-08** | **Contrast Ratio (SC 1.4.3)** | Kontras warna teks utama terhadap latar belakang | Rasio kontras teks utama (`#0f172a` pada latar `#ffffff` atau `#f8fafc`) melebihi 4.5:1 | Kontras tajam dan mudah dibaca | **PASS** |
| **A11Y-09** | **Image Alt Text (SC 1.1.1)** | Pemeriksaan elemen gambar katalog destinasi | Semua elemen `<img>` memiliki atribut `alt` yang mendeskripsikan nama destinasi | Alt text terisi sesuai nama tempat wisata | **PASS** |
| **A11Y-10** | **Status Messages (SC 4.1.3)** | Penambahan tujuan baru pada kalkulator | Daftar dinamis memiliki `aria-live="polite"` agar perubahan daftar diumumkan kepada screen reader | Notifikasi perubahan status diterima assistive tech | **PASS** |

---

## 5. Konfigurasi Pagination Bootstrap 5
Laravel 12 secara bawaan menggunakan Tailwind CSS untuk styling pagination (`$paginator->links()`). Untuk memastikan keselarasan visual 100% dengan Bootstrap 5:
- Telah didaftarkan konfigurasi global pada `app/Providers/AppServiceProvider.php`:
  ```php
  use Illuminate\Pagination\Paginator;

  public function boot(): void
  {
      Paginator::useBootstrapFive();
  }
  ```
- Seluruh pemanggilan pagination publik dan admin (`$destinations->links()`, `$regions->links()`, `$categories->links()`) kini menghasilkan markup HTML murni Bootstrap 5 (`.pagination`, `.page-item`, `.page-link`) yang responsif dan konsisten.

---

## 6. Kesimpulan Hasil Verifikasi (QA Sign-Off)
- **Total Skenario Pengujian Responsivitas:** 10 Skenario (**10 PASS / 100%**)
- **Total Skenario Pengujian Aksesibilitas:** 10 Skenario (**10 PASS / 100%**)
- **Horizontal Overflow Bug:** 0 elemen (Terverifikasi bebas scrolling horizontal pada lebar 320px–430px)
- **Status Akhir:** **APPROVED FOR MVP PRODUCTION DEPLOYMENT**
