# Ringkasan Eksekutif Deployment cPanel (Deployment Quick Reference)
**Proyek:** Website Layanan Private Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 17)  
**Dokumen Terkait:** [`docs/cpanel-deployment-guide.md`](cpanel-deployment-guide.md) & [`docs/deployment-git-cpanel.md`](deployment-git-cpanel.md)  

---

## 1. Ikhtisar Alur Deployment

```mermaid
flowchart LR
    A[Build Lokal & Test] -->|composer install --no-dev| B[Siapkan Paket Rilis]
    B -->|Upload Git / ZIP| C[Server cPanel]
    C -->|Atur Document Root| D[/home/USER/tour-bali-app/public]
    D -->|Migrasi & Cache| E[php artisan optimize]
    E --> F[Smoke Test Produksi]
```

---

## 2. Checklist Singkat Deployment

1. **PHP:** MultiPHP PHP 8.3 dengan ekstensi `pdo_mysql`, `curl`, `fileinfo`, `gd`, `mbstring`, `zip`.
2. **Document Root:** Wajib diarahkan ke `/home/USERNAME/tour-bali-app/public`.
3. **Database:** Buat via cPanel MySQL Databases, catat nama database ber-prefix cPanel.
4. **Environment:** File `.env` diletakkan di `/home/USERNAME/tour-bali-app/.env` (`APP_DEBUG=false`, `APP_ENV=production`).
5. **Storage Link:** Jalankan `php artisan storage:link` (atau script helper symlink jika SSH diblokir).
6. **Migrasi Database:** Jalankan `php artisan migrate --force`.
7. **Cache Kompilasi:** Jalankan `php artisan optimize`.
8. **Verifikasi:** Eksekusi 30 poin pengujian pada [`docs/production-smoke-test.md`](production-smoke-test.md).

> Untuk panduan langkah demi langkah yang sangat mendalam dan penanganan troubleshooting hosting, baca [Panduan Lengkap cPanel Deployment](cpanel-deployment-guide.md).
