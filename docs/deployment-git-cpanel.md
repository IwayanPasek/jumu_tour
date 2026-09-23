# Panduan Deployment Berulang Melalui Git cPanel Version Control
**Proyek:** Website Layanan Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 16)  
**Target:** cPanel Git™ Version Control Engine (`.cpanel.yml`)  
**Tanggal Terbit:** 23 September 2026  

---

## 1. Prasyarat Deployment
Sebelum melakukan deployment otomatis atau pembaruan rilis melalui fitur cPanel Git Version Control, pastikan hal-hal berikut telah terpenuhi:
1. **Repository Git Siap:** Proyek memiliki branch `main` (untuk production) dan `development` (untuk pengerjaan fitur/perbaikan).
2. **Koneksi Remote Repository:** Repository remote (GitHub, GitLab, atau Git lokal cPanel) telah terhubung pada cPanel melalui menu **Git™ Version Control**.
3. **Konfigurasi Document Root:** Document Root domain pada cPanel telah diarahkan ke `/home/USERNAME/tour-bali-app/public` (bukan langsung ke `public_html`).
4. **File `.cpanel.yml` Valid:** File `.cpanel.yml` berada di root repository dengan variabel `DEPLOYPATH` yang sudah disesuaikan dengan username cPanel server.
5. **Lingkungan Server:** PHP 8.3/8.2 aktif, ekstensi lengkap, database MariaDB/MySQL telah terkonfigurasi.

---

## 2. Kebijakan Percabangan (Branching Policy)
- **Branch `development`:** Digunakan untuk seluruh aktivitas pengembangan fitur, perbaikan bug (*bugfix*), dan pengujian lokal.
- **Branch `main`:** Digunakan secara eksklusif untuk rilis production yang telah teruji 100% pada automated test suite.
- **Aturan Rilis:**
  1. Dilarang melakukan *force push* (`git push --force`) ke branch `main`.
  2. Dilarang melakukan commit file `.env`, file kredensial, atau data pelanggan.
  3. Setiap rilis production ditandai dengan Git Tag versi semantik sederhana (contoh: `git tag -a v1.0.0 -m "Release MVP v1.0.0"`).

---

## 3. Prosedur Standar Deployment Berulang (Step-by-Step SOP)

### Langkah 1: Backup Database Production
Sebelum menarik (*pull*) commit baru di production, amankan data transaksi dan master data:
- Buka menu **phpMyAdmin** di cPanel.
- Pilih database aplikasi (misal: `usercpanel_tourdb`).
- Klik tab **Export**, pilih metode **Quick**, dan klik **Export** (simpan file `.sql` di komputer lokal atau folder penyimpanan aman di luar web root).

### Langkah 2: Backup File `.env` & Release Sebelumnya
- Buka **File Manager** cPanel, masuk ke `/home/USERNAME/tour-bali-app/`.
- Salin file `.env` ke direktori aman (misal: `/home/USERNAME/backups/env-backup-$(date +%F)`).
- **PENTING:** Jangan pernah menyimpan cadangan `.env` di dalam folder `public/` atau `public_html/`.

### Langkah 3: Push Commit Teruji ke Branch `main`
Di komputer lokal:
```bash
# 1. Pastikan seluruh automated tests berhasil
php artisan test

# 2. Pastikan working tree bersih
git status

# 3. Merge branch development ke main lalu push
git checkout main
git merge development
git push origin main
git push origin --tags
```

### Langkah 4: Buka Menu Git™ Version Control di cPanel
1. Masuk ke cPanel dashboard.
2. Cari dan klik ikon **Git™ Version Control** di bagian *Files*.
3. Temukan repository proyek `tour-bali-app` pada daftar repository, lalu klik tombol **Manage**.

### Langkah 5: Tarik Pembaruan (Pull / Update)
1. Buka tab **Basic Info** atau **Pull or Deploy**.
2. Pastikan branch yang aktif terpilih adalah **`main`**.
3. Klik tombol **Update from Remote** (Git Pull). cPanel akan mengunduh commit terbaru dari remote repository.

### Langkah 6: Eksekusi Deployment (Deploy HEAD Commit)
1. Buka tab **Pull or Deploy**.
2. Di bagian *Deploy HEAD Commit*, klik tombol **Deploy HEAD Commit**.
3. cPanel Deployment Engine akan membaca file `.cpanel.yml` dan mengeksekusi sinkronisasi file ke `DEPLOYPATH`.

### Langkah 7: Periksa Log Deployment cPanel
Setelah proses deployment selesai:
- Periksa log keluaran di cPanel. File log deployment biasanya disimpan di `/home/USERNAME/.cpanel/logs/` dengan penamaan `vc_TIMESTAMP_git_deploy.log`.
- Pastikan tidak ada pesan error rsync atau permission denied.

### Langkah 8: Eksekusi Migrasi Database (Jika Diperlukan)
Jika commit baru menyertakan migrasi schema tabel:
- Melalui **Terminal / SSH**:
  ```bash
  cd ~/tour-bali-app
  php artisan migrate --force
  ```
- **Aturan:** Dilarang keras menjalankan `migrate:fresh` atau `db:wipe`.

### Langkah 9: Bangun Ulang Cache Aplikasi (Optimization Cache)
Perbarui cache konfigurasi, route, dan view agar perubahan kode segera terbaca oleh pengunjung:
```bash
php artisan optimize:clear
php artisan optimize
```

### Langkah 10: Verifikasi Hak Akses Direktori Storage
Pastikan hak akses folder storage tetap terjaga:
```bash
chmod -R 755 ~/tour-bali-app/storage ~/tour-bali-app/bootstrap/cache
```

### Langkah 11: Eksekusi Smoke Test Produksi
Buka lembar uji [`docs/production-smoke-test.md`](file:///h:/Template%20Project/jumu_tour/docs/production-smoke-test.md) dan lakukan uji cepat pada:
- Halaman beranda (`/`).
- Halaman kalkulator rute dan peta interaktif (`/calculator`).
- Simulasi klik tombol pemesanan WhatsApp (`/booking/whatsapp`).
- Dashboard administrator (`/admin/dashboard`).
- Tampilan responsif pada perangkat ponsel (360px–430px).

---

## 4. Format Pencatatan Rilis (Release Log Format)
Setiap kali deployment selesai dilakukan, catat rekapitulasi pada log rilis internal:

```text
==================================================
LOG DEPLOYMENT PRODUKSI
==================================================
Versi Rilis      : v1.0.0-MVP
Commit Hash      : e4f9a1c...
Waktu Deployment : 23 September 2026, 14:00 WITA
Pelaksana        : Administrator Sistem
Branch           : main
Status Migrasi   : Tidak ada migrasi baru / Migrasi berhasil
Hasil Smoke Test : 30/30 PASS (100%)
Kendala Lapangan : Tidak ada kendala
Catatan Khusus   : Cache konfigurasi & route berhasil diperbarui
==================================================
```

---

## 5. Prosedur Rollback Cepat (Quick Rollback SOP)
Jika terjadi error fatal pada saat atau setelah deployment rilis baru:

1. **Aktifkan Maintenance Mode (Opsional untuk mencegah transaksi anomali):**
   ```bash
   php artisan down --secret="kunci-bypass-anda"
   ```
2. **Rollback Commit Melalui cPanel Git:**
   - Di cPanel **Git™ Version Control**, Anda dapat memilih commit stabil sebelumnya (*Previous Commit Hash*) lalu klik **Deploy**.
   - Atau melalui terminal:
     ```bash
     cd ~/tour-bali-app
     git checkout TAG_VERSI_SEBELUMNYA # contoh: v0.9.0
     ```
3. **Rollback Database (Hanya jika migrasi baru bermasalah):**
   - Jika migrasi baru merusak kompatibilitas data, restore database dari file `.sql` cadangan yang dibuat pada *Langkah 1* melalui menu phpMyAdmin.
   - **Peringatan:** Jangan menjalankan `php artisan migrate:rollback` tanpa memahami implikasi hilangnya data baru.
4. **Rebuild Cache:**
   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```
5. **Nonaktifkan Maintenance Mode:**
   ```bash
   php artisan up
   ```
6. **Verifikasi Ulang:** Lakukan pengetesan cepat pada halaman utama dan kalkulator.
