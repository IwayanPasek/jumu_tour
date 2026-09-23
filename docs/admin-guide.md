# Buku Panduan Pengelola (Admin Guide)
**Proyek:** Website Layanan Private Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 17)  
**Target Pembaca:** Administrator & Operator Layanan Tour Bali  

---

## 1. Akses Masuk & Dashboard Admin
1. **Membuka Halaman Login:**
   - Kunjungi URL: `https://domain-anda.example/admin/login`
   - Masukkan alamat email dan password administrator Anda.
   - Klik tombol **Masuk ke Panel**.
2. **Keamanan Login:**
   - Sistem dilengkapi proteksi *Rate Limiting*. Jika memasukkan password yang salah sebanyak 5 kali berturut-turut, akses akan dikunci sementara selama 60 detik.
3. **Dashboard Administrator:**
   - Setelah berhasil login, Anda akan diarahkan ke `/admin/dashboard`.
   - Menampilkan ringkasan metrik: Total Daerah, Total Kategori, Total Destinasi Aktif, dan Status Sistem.

---

## 2. Pengelolaan Daerah Wisata (Regions)
Menu ini digunakan untuk mengelompokkan destinasi berdasarkan wilayah di Bali (misal: Kuta, Ubud, Seminyak, Nusa Dua).

- **Melihat Daftar Daerah:** Klik menu **Kelola Daerah** di navigasi samping.
- **Menambah Daerah Baru:**
  1. Klik tombol **Tambah Daerah**.
  2. Masukkan nama daerah (contoh: *Sanur*).
  3. Masukkan nama kabupaten (contoh: *Denpasar*).
  4. Isi deskripsi singkat wilayah.
  5. Klik **Simpan Daerah**. (Slug URL akan dibuatkan otomatis secara unik).
- **Mengubah Data Daerah:** Klik ikon pensil (**Edit**) pada baris daerah yang bersangkutan.
- **Mengaktifkan / Menonaktifkan Daerah:** Klik badge status (Aktif/Nonaktif) pada tabel untuk mengubah status secara instan tanpa reload halaman.
- **Menghapus Daerah:** Klik ikon tong sampah (**Hapus**). Jika daerah tersebut masih memiliki destinasi wisata, sistem akan menolak penghapusan demi menjaga integritas data.

---

## 3. Pengelolaan Kategori Wisata (Categories)
Menu ini digunakan untuk mengelompokkan tempat wisata berdasarkan tema wisata (misal: Pantai & Bahari, Pura & Budaya, Kuliner, Alam).

- **Menambah Kategori Baru:** Klik **Tambah Kategori**, isi nama kategori dan deskripsi singkat, lalu klik **Simpan Kategori**.
- **Status Kategori:** Kategori yang dinonaktifkan tidak akan muncul pada filter publik di website.

---

## 4. Pengelolaan Tempat Wisata (Destinations)
Menu utama untuk mengelola katalog destinasi wisata yang dapat dipilih oleh wisatawan pada kalkulator rute.

- **Menambah Destinasi Baru:**
  1. Klik tombol **Tambah Destinasi**.
  2. **Nama Wisata:** Masukkan nama tempat wisata (contoh: *Pura Ulun Danu Beratan*).
  3. **Pilih Daerah:** Pilih wilayah lokasi wisata berada.
  4. **Pilih Kategori:** Pilih tema kategori terkait.
  5. **Koordinat Geografis:**
     - Masukkan **Latitude** (contoh: `-8.2751500`).
     - Masukkan **Longitude** (contoh: `115.1659700`).
     - *Tips:* Buka Google Maps, klik kanan pada lokasi tempat wisata, lalu salin koordinat latitude dan longitude.
  6. **Foto Utama:** Unggah file gambar berformat `.jpg`, `.jpeg`, `.png`, atau `.webp` dengan ukuran maksimal 5 MB.
  7. **Urutan Tampilan (*Display Order*):** Tentukan angka prioritas urutan (angka lebih kecil tampil lebih awal di katalog).
  8. Klik **Simpan Destinasi**.
- **Mengganti / Menghapus Foto:**
  - Buka halaman Edit Destinasi.
  - Untuk mengganti foto, pilih file baru pada input foto.
  - Untuk menghapus foto tanpa mengganti, klik tombol **Hapus Foto**. Sistem akan otomatis menghapus file lama dari disk server.

---

## 5. Keluar dari Panel Admin (Logout)
- Klik tombol **Keluar (Logout)** di bagian bawah sidebar navigasi admin.
- Demi alasan keamanan, proses logout menggunakan request POST yang memvalidasi token CSRF dan menghancurkan sesi di server secara tuntas.

---

## 6. Panduan Pemecahan Masalah (Troubleshooting)
1. **Lupa Password Admin:**
   - Masuk ke terminal/SSH cPanel, lalu jalankan:
     ```bash
     php artisan tinker
     $user = App\Models\User::where('is_admin', true)->first();
     $user->password = Hash::make('PasswordBaruAnda123!');
     $user->save();
     ```
2. **Gambar yang Diunggah Tidak Muncul di Website Publik:**
   - Periksa apakah symbolic link storage sudah dibuat. Jalankan perintah `php artisan storage:link` pada server.
   - Pastikan permission folder `storage/app/public` bernilai 755.
3. **Peta di Kalkulator Tidak Menampilkan Rute:**
   - Periksa apakah kuota Google Cloud Console Anda masih aktif.
   - Pastikan `GOOGLE_MAPS_SERVER_KEY` di file `.env` memiliki izin akses ke Google Routes API.
