# Batasan Sistem & Ruang Lingkup MVP (Known Limitations)
**Proyek:** Website Layanan Private Tour Bali (Jumu Bali Tour MVP)  
**Versi:** 1.0.0-MVP (Cluster 17)  
**Tujuan Dokumen:** Menegaskan batasan teknis dan fungsional produk minimum yang layak (MVP) guna menyelaraskan ekspektasi pengembang, operator bisnis, dan pengguna akhir.  

---

## 1. Batasan Fungsional Bisnis (Functional Boundaries)

1. **Tidak Ada Payment Gateway / Pembayaran Online:**
   - Website MVP tidak memproses pembayaran digital (kartu kredit, Virtual Account, e-wallet, atau QRIS).
   - Kesepakatan uang muka (DP) atau pelunasan sewa dilakukan secara langsung antara wisatawan dan operator melalui percakapan WhatsApp atau pembayaran tunai kepada supir.
2. **Tidak Ada Akun Pelanggan (Guest-Only Booking):**
   - Wisatawan tidak memiliki halaman profil, riwayat pesanan, atau sistem login. Perencanaan rute bersifat langsung (*frictionless*).
3. **Tidak Ada Status Pesanan / Dashboard Booking:**
   - Sistem tidak menyimpan transaksi reservasi ke database internal (*stateless booking*). Manajemen jadwal tour dikelola oleh operator melalui WhatsApp dan kalender operasional manual.
4. **Tidak Ada Fitur Obrolan Internal (Live Chat):**
   - Komunikasi instan dialihkan sepenuhnya ke aplikasi WhatsApp resmi milik operator via tautan Click-to-Chat.
5. **Harga Bersifat Estimasi Transparan:**
   - Formula tarif menghitung estimasi biaya dasar sewa, jarak tempuh, dan penumpang tambahan. Biaya belum mencakup tiket masuk objek wisata, wahana air, biaya penyeberangan kapal cepat, atau tiket parkir khusus yang dapat berubah sewaktu-waktu.
6. **Tidak Ada Integrasi Tiket Wisata Elektronik (e-Ticketing):**
   - Tiket masuk objek wisata (seperti tari Kecak Uluwatu atau tiket pura) dibeli langsung oleh wisatawan di loket resmi destinasi atau dipaketkan manual oleh operator.
7. **Tidak Ada Manajemen Supir / Armada (Driver & Fleet Assignment):**
   - Tidak ada modul penugasan supir atau pelacakan armada GPS di sistem. Penugasan supir dilakukan secara manual oleh manajemen tour.

---

## 2. Batasan Algoritma & API Eksternal (Technical Boundaries)

1. **Urutan Rute Manual (Tanpa Optimasi Rute Otomatis):**
   - Sistem tidak menggunakan algoritma pemecah *Traveling Salesperson Problem* (TSP) atau Google Route Matrix untuk mencari urutan rute terpendek secara otomatis.
   - Urutan perjalanan 100% mengikuti keinginan dan kebebasan wisatawan melalui tombol panah "Naik" dan "Turun".
2. **Ketergantungan pada Google Routes API:**
   - Keakuratan jarak (km) dan durasi perjalanan bergantung sepenuhnya pada tanggapan resmi Google Routes API dan kondisi lalu lintas Bali saat kueri dikirimkan.
   - Kemacetan musiman (misal: saat musim liburan di area Canggu atau Ubud) dapat memperpanjang durasi riil perjalanan di lapangan.
3. **Tindakan Pengiriman WhatsApp Masih Manual:**
   - Tautan `wa.me` hanya membuka aplikasi WhatsApp dan mengisi draf teks rute. Pengguna wajib menekan tombol "Kirim (Send)" secara sadar. Sistem tidak dapat mengirim pesan tanpa intervensi pengguna.
4. **Maksimal 5 Destinasi Per Rencana Tour:**
   - Sistem membatasi rute dalam satu paket harian maksimal 5 titik wisata demi menjaga keselamatan perjalanan supir, waktu kunjungan yang realistis bagi wisatawan, dan efisiensi kuota API.

---

## 3. Batasan Infrastruktur Shared Hosting (Infrastructure Limitations)

1. **Keterbatasan Sumber Daya Shared cPanel:**
   - Server shared hosting berbagi CPU, RAM, dan I/O disk dengan akun lain. Trafik lonjakan tinggi (*spike*) dapat mempengaruhi kecepatan respon jika hosting memiliki limit memori rendah.
2. **Tidak Ada Background Queue Worker (Redis / Horizon):**
   - Aplikasi menggunakan queue driver `sync` (eksekusi langsung) untuk menghindari ketergantungan pada proses daemon latar belakang yang kerap dimatikan oleh sistem shared hosting.
3. **Pencadangan Basis Data Otomatis:**
   - Mekanisme backup otomatis bergantung pada fitur cadangan cPanel mingguan/harian provider hosting. Pengelola disarankan melakukan ekspor SQL manual berkala sebelum memperbarui data besar.
