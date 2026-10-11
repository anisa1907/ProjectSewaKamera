# Blueprint & Project Scaffolding: Sistem Sewa Camera (MVC - CodeIgniter 4)

Berdasarkan dokumen Capstone Project (Kelompok 9 - Manajemen Inventory Sistem Sewa Camera), sistem ini dibangun menggunakan arsitektur **MVC murni**, dengan 3 aktor utama: **Owner**, **Kasir**, dan **Pelanggan**.

---

## 1. Project Scaffolding & Struktur Arsitektur MVC

```text
c:\Users\hp\ProjectSewaKamera\KodeCI4\
├── app/
│   ├── Config/
│   │   ├── Routes.php                   # Routing grup (Auth, Pelanggan, Kasir, Owner)
│   │   └── Filters.php                  # Filter autentikasi role & session guard
│   ├── Filters/
│   │   ├── AuthFilter.php               # Memastikan user sudah login
│   │   └── RoleFilter.php               # Membatasi akses: owner, kasir, pelanggan
│   ├── Database/
│   │   ├── Migrations/                  # 9 Tabel skema database sesuai rancangan
│   │   └── Seeds/
│   │       └── DatabaseSeeder.php       # Data awal: akun user (Owner, Kasir, Pelanggan), kategori, inventory & unit
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── KategoriModel.php
│   │   ├── InventoryModel.php
│   │   ├── UnitModel.php
│   │   ├── SewaModel.php
│   │   ├── DetailSewaModel.php
│   │   ├── PembayaranModel.php
│   │   └── PengembalianModel.php
│   │   └── MaintenanceModel.php
│   ├── Controllers/
│   │   ├── Auth.php                     # Login, register pelanggan, logout
│   │   ├── Pelanggan/
│   │   │   ├── Katalog.php              # Lihat daftar barang & unit tersedia
│   │   │   ├── Sewa.php                 # Buat pemesanan, upload/pilih jaminan
│   │   │   └── StatusSewa.php           # Cek status sewa, invoice & QRIS
│   │   ├── Kasir/
│   │   │   ├── Dashboard.php            # Monitor aktivitas & ringkasan operasional
│   │   │   ├── Kategori.php             # Master Data Kategori
│   │   │   ├── Inventory.php            # Master Data Inventory
│   │   │   ├── Unit.php                 # Master Data Unit fisik
│   │   │   ├── Transaksi.php            # Kelola sewa & verifikasi jaminan
│   │   │   ├── Pembayaran.php           # Verifikasi bayar (Menunggu -> Terbayar / Ditolak)
│   │   │   ├── Pengembalian.php         # Terima unit, denda, kondisi (baik/rusak)
│   │   │   └── Maintenance.php          # Catat & pantau perbaikan unit kamera/drone
│   │   └── Owner/
│   │       ├── Dashboard.php            # Metrik finansial & operasional
│   │       └── Laporan.php              # Filter periode, rekap sewa, cetak laporan & export
│   └── Views/
│       ├── layout/                      # Template base, navbar, sidebar, flash alert
│       ├── auth/                        # Halaman Login & Registrasi
│       ├── pelanggan/                   # Katalog barang, checkout sewa, status sewa, cetak invoice QRIS
│       ├── kasir/                       # Dashboard, CRUD master, kelola sewa, verifikasi pembayaran, retur, maintenance
│       └── owner/                       # Dashboard analitik & cetak laporan periode
├── public/
│   └── uploads/                         # Direktori upload jaminan & bukti
└── .env                                 # Konfigurasi database MySQL & CI_ENVIRONMENT
```

---

## 2. Todo & Task Breakdown

### Fase 1: Database & Environment Scaffolding
- [x] Sinkronisasi branch & verifikasi file migrasi 9 tabel
- [ ] Buat file konfigurasi `.env` dengan kredensial database lokal
- [ ] Buat `DatabaseSeeder.php` dengan data akun default (owner, kasir, pelanggan) dan contoh unit kamera/drone
- [ ] Buat helper untuk format mata uang Rupiah & generate QRIS payload/image

### Fase 2: Autentikasi & Authorization Guard
- [ ] Buat `AuthFilter.php` & `RoleFilter.php`
- [ ] Buat `Controllers/Auth.php` (login, logout, register pelanggan)
- [ ] Buat view login & registrasi dengan UI responsif modern

### Fase 3: Data Layer (Models)
- [ ] Model `UserModel`, `KategoriModel`, `InventoryModel`, `UnitModel`
- [ ] Model `SewaModel`, `DetailSewaModel`, `PembayaranModel`
- [ ] Model `PengembalianModel`, `MaintenanceModel`

### Fase 4: Fitur Aktor Pelanggan
- [ ] Katalog produk & unit kamera/lensa/drone dengan status real-time
- [ ] Formulir sewa (pilih unit, tanggal sewa - tanggal kembali, pilihan jaminan: KTP/KK/Akta, metode: QRIS/Cash)
- [ ] Halaman Riwayat & Detail Sewa
- [ ] **Fitur Cetak Invoice + QRIS Dinamis**: Tampilan invoice siap cetak (Print PDF/Thermal) dengan QR code pembayaran

### Fase 5: Fitur Aktor Kasir
- [ ] CRUD Master Data: Kategori, Inventory (Produk), Unit fisik
- [ ] Kelola Transaksi: Verifikasi penyerahan jaminan & serah terima unit (`status_unit: disewa`)
- [ ] Kelola Pembayaran: Verifikasi pembayaran (`menunggu` -> `terbayar` / `ditolak`)
- [ ] Pengembalian Unit: Cek kondisi unit, hitung denda keterlambatan/kerusakan, kembalikan jaminan
- [ ] Maintenance Unit: Jika barang kembali dalam kondisi rusak, otomatis/manual masuk antrean perbaikan

### Fase 6: Fitur Aktor Owner
- [ ] Dashboard analitik omzet & status armada kamera
- [ ] Laporan transaksi sewa (filter berdasarkan rentang tanggal/periode)
- [ ] Laporan pembayaran & pendapatan kasir
- [ ] Cetak / Print View Laporan Resmi untuk Owner

---

## 3. Data Akun Default untuk Pengujian

| Role | Username | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Owner** | `owner` | `owner123` | Akses laporan & analitik eksekutif |
| **Kasir** | `kasir` | `kasir123` | Akses master data, verifikasi transaksi & maintenance |
| **Pelanggan** | `nazwa` | `nazwa123` | Akses katalog, pemesanan, dan invoice QRIS |
