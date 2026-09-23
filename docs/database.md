# Skema & Desain Basis Data (Database Documentation)
**Proyek:** Website Layanan Private Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 17)  
**RDBMS Target:** MySQL 8.0+ / MariaDB 10.4+  
**Karakter Set:** `utf8mb4`  
**Collation:** `utf8mb4_unicode_ci`  

---

## 1. Diagram Hubungan Entitas Teks (Text ERD)

```text
+-----------------------+          +---------------------------+
|        REGIONS        |          |        CATEGORIES         |
+-----------------------+          +---------------------------+
| id (PK)               |          | id (PK)                   |
| name (VARCHAR)        |          | name (VARCHAR)            |
| slug (VARCHAR, UNIQUE)|          | slug (VARCHAR, UNIQUE)    |
| regency (VARCHAR)     |          | description (TEXT)        |
| is_active (BOOLEAN)   |          | is_active (BOOLEAN)       |
+-----------+-----------+          +-------------+-------------+
            | 1                                  | 1
            |                                    |
            | N (restrictOnDelete)               | N (nullOnDelete)
+-----------v------------------------------------v-------------+
|                         DESTINATIONS                         |
+--------------------------------------------------------------+
| id (PK)                                                      |
| region_id (FK -> regions.id)                                 |
| category_id (FK -> categories.id, NULLABLE)                  |
| name (VARCHAR)                                               |
| slug (VARCHAR, UNIQUE)                                       |
| description (TEXT)                                           |
| address (TEXT, NULLABLE)                                     |
| latitude (DECIMAL 10,7)                                      |
| longitude (DECIMAL 10,7)                                     |
| image_path (VARCHAR, NULLABLE)                               |
| display_order (INTEGER, DEFAULT 0)                           |
| is_active (BOOLEAN, DEFAULT true)                            |
+--------------------------------------------------------------+

+--------------------------------+   +----------------------------------+
|     PRICING_CONFIGURATIONS     |   |          BRAND_SETTINGS          |
+--------------------------------+   +----------------------------------+
| id (PK)                        |   | id (PK)                          |
| name (VARCHAR)                 |   | brand_name (VARCHAR)             |
| base_price (DECIMAL 12,2)      |   | tagline (VARCHAR, NULLABLE)      |
| price_per_km (DECIMAL 12,2)    |   | logo_path (VARCHAR, NULLABLE)    |
| extra_passenger_fee (DECIMAL)  |   | primary_color (VARCHAR)          |
| max_passengers (INTEGER)       |   | secondary_color (VARCHAR)        |
| minimum_price (DECIMAL 12,2)   |   | whatsapp_number (VARCHAR)        |
| is_active (BOOLEAN)            |   | email (VARCHAR, NULLABLE)        |
+--------------------------------+   | is_active (BOOLEAN)              |
                                     +----------------------------------+

+--------------------------------+
|             USERS              |
+--------------------------------+
| id (PK)                        |
| name (VARCHAR)                 |
| email (VARCHAR, UNIQUE)        |
| password (VARCHAR, Hashed)     |
| is_admin (BOOLEAN, DEFAULT 0)  |
| is_active (BOOLEAN, DEFAULT 1) |
+--------------------------------+
```

---

## 2. Rincian Tabel & Kolom Kunci

### A. Tabel `regions` (Wilayah Wisata Bali)
- `id`: Primary key (BIGINT UNSIGNED).
- `name`: Nama daerah (contoh: Kuta, Ubud, Nusa Dua, Canggu).
- `slug`: Slug ramah URL unik (`unique()`, `index()`).
- `regency`: Nama kabupaten administratif di Bali (Badung, Gianyar, Tabanan, dll).
- `is_active`: Penanda status ketersediaan wilayah di publik.

### B. Tabel `categories` (Kategori Wisata)
- `id`: Primary key (BIGINT UNSIGNED).
- `name`: Nama kategori (contoh: Pantai & Bahari, Pura & Budaya, Alam & Air Terjun).
- `slug`: Slug unik untuk filter kategori publik.
- `is_active`: Penanda status kategori.

### C. Tabel `destinations` (Tempat Wisata)
- `id`: Primary key (BIGINT UNSIGNED).
- `region_id`: Foreign key merujuk ke `regions.id`. Wajib diisi.
- `category_id`: Foreign key merujuk ke `categories.id`. Nullable jika kategori dihapus.
- `name`: Nama tempat wisata (Tanah Lot, Pura Besakih, Pantai Pandawa).
- `slug`: Slug unik untuk halaman detail publik.
- `latitude` & `longitude`: Koordinat presisi geografis (`decimal(10, 7)`).
- `image_path`: Path file foto pada storage publik (`storage/app/public/destinations`).
- `display_order`: Urutan prioritas penayangan di katalog publik.
- `is_active`: Flag status publikasi tempat wisata.

### D. Tabel `pricing_configurations` (Paket Tarif Dasar)
- `base_price`: Biaya sewa dasar mobil dan supir (tercakup 1 penumpang).
- `price_per_km`: Tarif jarak tempuh jalan raya per kilometer.
- `extra_passenger_fee`: Biaya tambahan per orang untuk penumpang ke-2 dan seterusnya.
- `minimum_price`: Tarif minimum sewa (mencegah tarif terlalu rendah pada jarak sangat dekat).
- `is_active`: Flag paket tarif yang sedang berlaku.

### E. Tabel `brand_settings` (Konfigurasi Identitas Brand)
- Menyimpan nama brand, tagline, logo, skema warna visual, dan nomor kontak resmi WhatsApp.

### F. Tabel `users` (Akun Administrator)
- Menggunakan `is_admin = true` dan `is_active = true` untuk memproteksi akses dashboard manajemen.

---

## 3. Kebijakan Integritas Relasi & Penghapusan
1. **Relasi `regions` ke `destinations`:**
   - Menggunakan aturan **`restrictOnDelete()`**.
   - Sebuah daerah tidak dapat dihapus jika masih terdapat tempat wisata yang terhubung. Hal ini menjaga integritas katalog dan mencegah data destinasi menjadi yatim (*orphan records*).
2. **Relasi `categories` ke `destinations`:**
   - Menggunakan aturan **`nullOnDelete()`**.
   - Jika sebuah kategori dihapus, destinasi terkait tetap aman dengan nilai kategori diset ke `NULL`.

---

## 4. Pengindeksan (Database Indexes)
- Index otomatis pada seluruh Primary Key dan Foreign Key (`region_id`, `category_id`).
- Index unik pada seluruh kolom `slug` di tabel `regions`, `categories`, dan `destinations`.
- Index pada kolom `is_active` dan `display_order` guna mempercepat eksekusi query pencarian katalog publik.

---

## 5. Daftar Seeder Database
- `AdminUserSeeder`: Membuat 1 akun administrator utama.
- `RegionSeeder`: Mengisi 8 daerah wisata strategis di Bali.
- `CategorySeeder`: Mengisi 8 kategori wisata tematik.
- `DestinationSeeder`: Mengisi 15 tempat wisata populer lengkap dengan koordinat riil.
- `PricingConfigurationSeeder`: Mengisi konfigurasi tarif sewa private tour default.
- `BrandSettingSeeder`: Mengisi konfigurasi brand identity "Bali Tour Service".
