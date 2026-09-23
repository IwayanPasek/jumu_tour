# Lembar Uji Penerimaan Pengguna (User Acceptance Test - UAT)
**Proyek:** Website Layanan Private Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 17 Final Audit)  
**Metodologi:** Black-Box Acceptance Testing & Role-Based Scenario Verification  
**Tanggal Uji:** 23 September 2026  
**Status Evaluasi:** **100% ACCEPTED (Semua Skenario Lolos Verifikasi)**

---

## 1. Matriks UAT: Skenario Pengguna Publik (Public User Journey)

| No | Modul / Fitur | URL / Endpoint | Langkah Pengujian | Expected Result | Actual Result | Status | Severity | Catatan / Solusi |
|---|---|---|---|---|---|---|---|---|
| **UAT-PUB-01** | Beranda Publik | `/` | Buka URL utama website di peramban (desktop & mobile). | Tampil hero banner, branding nama/logo, navigasi, dan cuplikan informasi tour private Bali. | Halaman beranda termuat cepat, responsif, dan layout rapi. | **PASS** | None | Memuat Bootstrap 5 CDN & custom CSS. |
| **UAT-PUB-02** | Katalog Destinasi | `/destinations` | Klik menu "Tempat Wisata" di navbar. | Tampil daftar seluruh destinasi wisata aktif dengan kartu informasi, foto, badge daerah, dan pagination Bootstrap 5. | Daftar destinasi tampil lengkap dengan pagination rapi. | **PASS** | None | Paginator Bootstrap 5 aktif. |
| **UAT-PUB-03** | Filter Daerah | `/regions` | Klik menu "Daerah" dan pilih salah satu daerah (misal: "Ubud"). | Menampilkan informasi wilayah Ubud serta daftar destinasi wisata yang berada di kawasan tersebut. | Destinasi di Ubud tersaring dengan benar. | **PASS** | None | Eager loading relasi mencegah N+1 query. |
| **UAT-PUB-04** | Filter Kategori | `/categories` | Klik menu "Kategori" dan pilih kategori (misal: "Pantai & Bahari"). | Menampilkan seluruh destinasi bertema pantai di Bali secara akurat. | Sesuai ekspektasi, hanya destinasi bahari yang tampil. | **PASS** | None | Scope kategori aktif berfungsi. |
| **UAT-PUB-05** | Detail Destinasi | `/destinations/tanah-lot` | Klik kartu destinasi "Tanah Lot" untuk membuka detail. | Menampilkan foto utama, deskripsi lengkap, alamat, koordinat, dan rekomendasi wisata sekitar. | Halaman detail memuat lengkap dan proporsional. | **PASS** | None | Rekomendasi menampilkan destinasi sedaerah. |
| **UAT-PUB-06** | Halaman Kalkulator | `/calculator` | Buka menu "Kalkulator Rute & Tarif" dari navbar atau tombol CTA. | Memuat formulir pemilihan titik jemput, dropdown tempat wisata, panel rute, dan peta Google Maps interaktif. | Halaman kalkulator terbuka dengan dua kolom (desktop) atau bertumpuk (mobile). | **PASS** | None | Peta interaktif siap menerima input. |
| **UAT-PUB-07** | Titik Jemput (Pickup) | `/calculator` | Pilih salah satu titik jemput populer (misal: "Bandara I Gusti Ngurah Rai") atau masukkan koordinat manual. | Marker jemput hijau bertanda huruf **P** muncul di peta pada posisi koordinat bandara. | Marker jemput terpasang di peta dan status rute terbarui. | **PASS** | None | Validasi koordinat geografis aktif. |
| **UAT-PUB-08** | Tambah Satu Tujuan | `/calculator` | Pilih 1 destinasi (misal: "Pantai Kuta") lalu klik tombol "Tambah". | Destinasi masuk ke daftar runtutan (No. 1), marker merah berangka **1** muncul di peta. | Destinasi terdaftar dan tombol hitung rute aktif. | **PASS** | None | Kuota maksimal 5 tujuan dipantau. |
| **UAT-PUB-09** | Tambah Multi-Tujuan | `/calculator` | Tambahkan destinasi ke-2 (Tanah Lot) dan ke-3 (Pura Uluwatu). | Daftar perjalanan memuat 3 destinasi berurutan, peta menampilkan 3 marker angka secara berurutan. | Tampil 3 tujuan berurutan di daftar dan peta. | **PASS** | None | Maksimal 5 tujuan dipatuhi. |
| **UAT-PUB-10** | Urutkan Tujuan Manual | `/calculator` | Klik tombol panah "Ke Atas" pada tujuan ke-2 (Tanah Lot). | Posisi Tanah Lot bertukar menjadi tujuan No. 1, nomor marker di peta ikut tersinkronisasi secara instan. | Urutan rute bertukar dengan mulus tanpa reload halaman. | **PASS** | None | Pengurutan manual tanpa auto-optimization. |
| **UAT-PUB-11** | Hitung Rute Server-Side | `/calculator/route` | Klik tombol "Hitung Rute & Estimasi Biaya". | Mengirim data ke backend, Google Routes API menghitung rute riil jalan raya, garis polyline biru tergambar di peta. | Polyline rute jalan raya tergambar menghubungkan jemput ke tujuan 1, 2, 3. | **PASS** | None | Server key terlindungi di backend. |
| **UAT-PUB-12** | Total Jarak & Durasi | `/calculator` | Periksa panel hasil kalkulasi setelah tombol hitung ditekan. | Menampilkan total jarak dalam kilometer (misal: 68.4 km) dan estimasi waktu tempuh jalan raya (misal: 2 jam 45 menit). | Informasi jarak dan durasi tampil presisi sesuai respons Routes API. | **PASS** | None | Durasi memperhitungkan estimasi traffic jalan. |
| **UAT-PUB-13** | Estimasi Biaya Sewa | `/calculator` | Pilih jumlah penumpang (misal: 4 orang) dan amati kartu estimasi harga. | Menampilkan rincian biaya dasar, biaya jarak, biaya penumpang tambahan, dan total estimasi berformat rupiah (IDR). | Rincian tarif transparan dengan pembulatan ke kelipatan Rp1.000 terdekat ke atas. | **PASS** | None | Formula pricing server-side konsisten. |
| **UAT-PUB-14** | Form Pemesanan Tour | `/calculator` | Isi data reservasi: Nama Lengkap, Nomor WhatsApp, Tanggal Tour (misal: esok hari), dan Catatan Khusus. | Seluruh kolom input tervalidasi dengan baik; validasi tanggal lampau ditolak secara elegan. | Form siap dikirim untuk membuat tautan WhatsApp. | **PASS** | None | Rate limiter `throttle:10,1` aktif. |
| **UAT-PUB-15** | Buka Tautan WhatsApp | `/booking/whatsapp` | Klik tombol "Pesan Sekarang via WhatsApp". | Aplikasi membuka jendela baru menuju `https://wa.me/628xxx` dengan parameter teks ter-encode rapi. | Browser dialihkan ke WhatsApp Web atau aplikasi WA mobile. | **PASS** | None | Nomor bisnis terformat internasional (628). |
| **UAT-PUB-16** | Periksa Pesan WhatsApp | Aplikasi WhatsApp | Tinjau draf pesan yang otomatis terisi pada ruang obrolan WhatsApp. | Pesan memuat: nama pemesan, tanggal tour, jumlah penumpang, rincian titik jemput & urutan destinasi, total jarak, durasi, estimasi biaya, dan disclaimer resmi. | Template pesan tersusun rapi, mudah dibaca, dan bebas dari bocoran kode. | **PASS** | None | Disclaimer harga estimasi tercantum. |
| **UAT-PUB-17** | Kirim Pesan Manual | Aplikasi WhatsApp | Pengguna menekan tombol "Send" di WhatsApp untuk mengirim pesan ke admin tour. | Pesan terkirim secara sadar oleh pengguna tanpa automasi spam background. | Pesan terkirim ke admin tour untuk negosiasi/konfirmasi jadwal. | **PASS** | None | Click-to-Chat resmi tanpa WhatsApp Business API berbayar. |

---

## 2. Matriks UAT: Skenario Administrator (Admin Operations)

| No | Modul / Fitur | URL / Endpoint | Langkah Pengujian | Expected Result | Actual Result | Status | Severity | Catatan / Solusi |
|---|---|---|---|---|---|---|---|---|
| **UAT-ADM-01** | Login Administrator | `/admin/login` | Masukkan email & password admin valid lalu klik Masuk. | Kredensial terverifikasi, sesi diregenerasi, diarahkan ke dashboard admin. | Berhasil login dan masuk ke dashboard. | **PASS** | None | Dilindungi rate limiter brute force. |
| **UAT-ADM-02** | Tambah Daerah Wisata | `/admin/regions/create` | Buat daerah baru "Nusa Penida" dengan deskripsi singkat. | Data tersimpan ke database, slug unik `nusa-penida` terbentuk otomatis, muncul di daftar daerah. | Daerah baru tersimpan dan berstatus aktif. | **PASS** | None | SlugService otomatis menangani keunikan. |
| **UAT-ADM-03** | Tambah Kategori Wisata | `/admin/categories/create` | Buat kategori baru "Wisata Kuliner". | Data kategori tersimpan, slug terbentuk otomatis, siap dipilih oleh destinasi. | Kategori baru tersimpan dan berstatus aktif. | **PASS** | None | Form request validasi nama unik. |
| **UAT-ADM-04** | Tambah Destinasi Wisata | `/admin/destinations/create` | Buat destinasi baru dengan nama, pilih daerah, kategori, koordinat latitude/longitude, dan deskripsi. | Destinasi baru berhasil ditambahkan dan masuk ke katalog sistem. | Data tersimpan lengkap dengan koordinat. | **PASS** | None | Validasi batas koordinat Bali aktif. |
| **UAT-ADM-05** | Unggah Foto Destinasi | `/admin/destinations/create` | Unggah file foto wisata berekstensi `.jpg` (ukuran 2.4 MB). | Gambar divalidasi, disimpan di disk storage publik dengan nama acak aman, thumbnail muncul. | File tersimpan di `storage/app/public/destinations`. | **PASS** | None | MIME type images tervalidasi ketat. |
| **UAT-ADM-06** | Validasi Koordinat Peta | `/admin/destinations/create` | Masukkan latitude di luar rentang valid (misal: `120.5`). | Formulir ditolak dengan pesan kesalahan validasi geografis yang ramah. | Validasi form request menolak input tidak valid. | **PASS** | None | Range latitude -90 s/d 90 dipatuhi. |
| **UAT-ADM-07** | Toggle Status Destinasi | `/admin/destinations` | Klik badge status "Aktif" pada salah satu destinasi di tabel. | Status berubah menjadi "Nonaktif" via PATCH request, destinasi otomatis tersembunyi di kalkulator publik. | Status berubah instan dan destinasi tersembunyi dari publik. | **PASS** | None | Flash message sukses muncul. |
| **UAT-ADM-08** | Edit Data Destinasi | `/admin/destinations/{id}/edit` | Ubah urutan tampilan (*display order*) atau perbarui deskripsi. | Data terperbarui di database dan langsung tercermin pada halaman publik. | Perubahan data tersimpan sempurna. | **PASS** | None | Form edit memuat data lama (*prefilled*). |
| **UAT-ADM-09** | Ganti / Hapus Gambar | `/admin/destinations/{id}` | Hapus foto lama destinasi melalui tombol hapus gambar. | File foto fisik di disk terhapus bersih tanpa menyisakan file yatim (*orphan file*), data destinasi beralih ke placeholder. | File foto terhapus dan placeholder tampil. | **PASS** | None | Storage disk cleanup bekerja. |
| **UAT-ADM-10** | Pencarian & Filter Data | `/admin/destinations?q=Ubud` | Cari tempat wisata dengan kata kunci "Ubud" dan filter daerah. | Tabel menampilkan hanya tempat wisata yang cocok dengan kriteria filter pencarian. | Hasil pencarian presisi dan tombol reset muncul. | **PASS** | None | Input pencarian di-escape aman. |
| **UAT-ADM-11** | Proteksi Hapus Daerah Terkait | `/admin/regions` | Coba hapus daerah yang masih memiliki destinasi wisata aktif. | Tombol hapus dicegah dan sistem menampilkan penolakan demi menjaga integritas data. | Penghapusan ditolak; foreign key integrity terjaga. | **PASS** | None | Aturan `restrictOnDelete` berjalan. |
| **UAT-ADM-12** | Logout Administrator | `/admin/logout` | Klik tombol "Keluar (Logout)" di sidebar navigasi admin. | Sesi admin dihancurkan, token CSRF di-refresh, dialihkan kembali ke form login. | Berhasil logout; akses URL admin setelahnya dialihkan. | **PASS** | None | Logout wajib menggunakan method POST. |

---

## 3. Kesimpulan Verifikasi UAT
- **Total Skenario Pengguna Publik:** 17 Skenario (**17 Lolos / 100%**)
- **Total Skenario Administrator:** 12 Skenario (**12 Lolos / 100%**)
- **Total Keseluruhan:** **29 Skenario UAT Lolos Tanpa Bug Kritis (0 Fail / 0 Blocker)**
- **Rekomendasi UAT:** Seluruh fungsionalitas MVP telah memenuhi kriteria penerimaan pengguna dan siap untuk diserahterimakan.
