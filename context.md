# Context Project: Sistem Manajemen Parkir

Dokumen ini menjelaskan konteks arsitektur, teknologi, struktur database, dan struktur kode dari aplikasi **Sistem Manajemen Parkir**.

---

## 1. Ringkasan Sistem

Sistem Manajemen Parkir ini dirancang untuk mendigitalisasi operasional area parkir guna meningkatkan efisiensi, mencegah kebocoran pendapatan, dan memberikan transparansi ketersediaan slot parkir secara real-time.

Sistem ini terbagi menjadi dua sisi utama:
1. **Sisi Publik (Landing Page)**: Antarmuka publik yang digunakan oleh pengunjung umum untuk memantau slot parkir kosong, melihat daftar tarif parkir terbaru, dan melakukan registrasi member baru.
2. **Sisi Internal (POS & Admin Panel)**: Aplikasi operasional yang digunakan oleh **Petugas Gerbang (Staff)** untuk mengelola kendaraan masuk/keluar serta **Admin/Manajer** untuk mengelola slot, tarif, member, akun petugas, dan melihat laporan pendapatan.

---

## 2. Tech Stack (Teknologi yang Digunakan)

Aplikasi dibangun menggunakan ekosistem modern Laravel:

| Layer | Teknologi / Library | Deskripsi |
|---|---|---|
| **Core Framework** | Laravel 13 (PHP 8.5) | Framework backend utama yang menangani logika bisnis, perutean, migrasi database, dan API. |
| **Autentikasi** | Laravel Fortify (v1) | Backend autentikasi yang aman untuk mengelola login, pendaftaran akun internal, verifikasi email, Two-Factor Authentication (2FA), serta dukungan Passkeys. |
| **UI Interaktif** | Livewire v4 & Alpine.js v3 | Framework full-stack Laravel untuk membuat antarmuka pengguna yang dinamis dan reaktif tanpa menulis JavaScript eksternal yang kompleks. |
| **Styling** | TailwindCSS v4 | Utility-first CSS framework untuk mendesain antarmuka UI internal dan eksternal secara kustom, responsif, dan konsisten. |
| **UI Components** | Flux UI (Free) v2 | Library komponen UI resmi untuk Livewire yang mempermudah pembuatan form, modal, tabel, tombol, dan komponen UI modern lainnya. |
| **Database** | MySQL / SQLite (untuk lokal/pengujian) | Penyimpanan data transaksi, slot parkir, riwayat tarif, data member, dan akun pengguna. |
| **Ekspor Laporan** | Laravel Excel | Library untuk mengekspor laporan transaksi bulanan/harian ke format spreadsheet (XLSX). |

---

## 3. Struktur Database (Skema Inti)

Database proyek ini (berdasarkan migrasi yang ada di [database/migrations](file:///d:/sistem-manajemen-parkir/database/migrations)) terdiri dari tabel-tabel berikut:

### A. `users`
Menyimpan data pengguna sistem yang terbagi menjadi dua peran (role): `admin` dan `staff`.
* **Kolom Utama**: `id`, `name`, `email`, `password`, `role` (enum: `admin`, `staff`), `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`.
* **Model**: [User.php](file:///d:/sistem-manajemen-parkir/app/Models/User.php)

### B. `parking_slots`
Menyimpan data seluruh slot parkir fisik yang terbagi per jenis kendaraan dan lokasi.
* **Kolom Utama**: `id`, `slot_code` (unique), `vehicle_type` (enum: `motor`, `mobil`, `truk`), `floor` (default '1'), `zone`, `status` (enum: `available`, `occupied`, `reserved`, `disabled`).
* **Model**: [ParkingSlot.php](file:///d:/sistem-manajemen-parkir/app/Models/ParkingSlot.php)

### C. `parking_rates`
Menyimpan pengaturan tarif aktif per jenis kendaraan.
* **Kolom Utama**: `id`, `vehicle_type` (enum: `motor`, `mobil`, `truk`, unique), `first_hour_rate`, `subsequent_hour_rate`, `daily_max_rate`, `fine_lost_ticket`.
* **Model**: [ParkingRate.php](file:///d:/sistem-manajemen-parkir/app/Models/ParkingRate.php)

### D. `parking_transactions`
Menyimpan data transaksi kendaraan parkir yang masuk dan keluar.
* **Kolom Utama**:
  * `id` (sekaligus digunakan sebagai Nomor Karcis/Tiket).
  * `slot_id` (foreign key ke `parking_slots`).
  * `vehicle_plate`, `vehicle_type`.
  * `entry_time`, `exit_time` (nullable).
  * **Snapshot Tarif** (disimpan saat kendaraan masuk agar tarif lama tidak berubah bagi kendaraan yang sedang parkir jika admin mengganti tarif tengah jalan): `snapshot_first_hour_rate`, `snapshot_subsequent_hour_rate`, `snapshot_daily_max_rate`, `snapshot_fine_lost_ticket`.
  * `fee` (total biaya parkir, dihitung otomatis saat keluar).
  * `payment_method` (cash, e-wallet, dll).
  * `officer_name` (nama petugas yang memproses entry/exit).
  * `status` (enum: `parked`, `exited`, `flagged`).
  * `duration_minutes` (generated column hasil kalkulasi selisih `exit_time` dan `entry_time`).
* **Model**: [ParkingTransaction.php](file:///d:/sistem-manajemen-parkir/app/Models/ParkingTransaction.php)

### E. `members`
Menyimpan data pendaftaran member langganan parkir.
* **Kolom Utama**: `id`, `full_name`, `vehicle_plate` (unique), `vehicle_type`, `phone`, `subscription_start` (nullable), `subscription_end` (nullable), `status` (enum: `pending`, `active`, `expired`).
* **Model**: [Member.php](file:///d:/sistem-manajemen-parkir/app/Models/Member.php)

### F. `rate_change_logs`
Menyimpan log perubahan tarif parkir yang dilakukan oleh Admin untuk audit keuangan.
* **Kolom Utama**: `id`, `vehicle_type`, `changed_by` (foreign key ke `users`), `old_rates` (json), `new_rates` (json), `created_at`.
* **Model**: [RateChangeLog.php](file:///d:/sistem-manajemen-parkir/app/Models/RateChangeLog.php)

---

## 4. Struktur Kode Proyek

### A. Routing & Middleware
* **Web Routes** ([routes/web.php](file:///d:/sistem-manajemen-parkir/routes/web.php)):
  * Rute publik `/` dialihkan secara dinamis ke halaman login (jika guest) atau `/pos/entry` (jika sudah terautentikasi).
  * Rute POS `/pos/entry` dan `/pos/exit` dilindungi oleh middleware `auth` sehingga dapat diakses oleh role `admin` maupun `staff`.
  * Rute Dashboard, Allotment, Members, Users, Rates, dan Laporan dilindungi oleh middleware `auth` dan middleware kustom `role:admin` ([CheckRole.php](file:///d:/sistem-manajemen-parkir/app/Http/Middleware/CheckRole.php)).
* **API Routes** ([routes/api.php](file:///d:/sistem-manajemen-parkir/routes/api.php)):
  * Rute API publik dilindungi oleh rate limiter `throttle:60,1` untuk mencegah penyalahgunaan.
  * Menyediakan endpoint bagi aplikasi Frontend Publik (React.js) untuk mengambil data ketersediaan slot parkir, info tarif aktif, dan mengirimkan form pendaftaran member.

### B. Livewire Components (`app/Livewire/`)
* **[EntryGate.php](file:///d:/sistem-manajemen-parkir/app/Livewire/EntryGate.php)**: Menangani pencatatan kendaraan masuk di gerbang masuk POS.
* **[ExitGate.php](file:///d:/sistem-manajemen-parkir/app/Livewire/ExitGate.php)**: Menangani kalkulasi biaya, input metode pembayaran, serta cetak struk dan penanganan karcis hilang di gerbang keluar POS.
* **[AdminDashboard.php](file:///d:/sistem-manajemen-parkir/app/Livewire/AdminDashboard.php)**: Mengumpulkan data statistik real-time (pendapatan hari ini, kapasitas parkir terisi, grafis kendaraan masuk per jam).
* **[AllotmentMap.php](file:///d:/sistem-manajemen-parkir/app/Livewire/AllotmentMap.php)**: Menyediakan peta visual tata letak slot parkir secara real-time dan memberikan kontrol manual bagi admin untuk mengubah status slot (override).
* **[MemberManagement.php](file:///d:/sistem-manajemen-parkir/app/Livewire/MemberManagement.php)**: Memvalidasi, mengaktifkan, dan menonaktifkan pendaftaran member parkir.
* **[RateManagement.php](file:///d:/sistem-manajemen-parkir/app/Livewire/RateManagement.php)**: Mengatur tarif per jenis kendaraan dan mencatat setiap perubahan ke dalam log.
* **[UserManagement.php](file:///d:/sistem-manajemen-parkir/app/Livewire/UserManagement.php)**: Mengelola akun administrator dan petugas gerbang (staff).
* **[ReportPage.php](file:///d:/sistem-manajemen-parkir/app/Livewire/ReportPage.php)**: Menyajikan laporan transaksi per periode (harian, mingguan, bulanan) dengan tombol ekspor data.

---

## 5. Integrasi Eksternal (API Layer)

Untuk mendukung landing page publik (React.js), proyek ini menyediakan 3 endpoint API utama di [ApiController.php](file:///d:/sistem-manajemen-parkir/app/Http/Controllers/Api/ApiController.php):

1. **`GET /api/available-slots`**:
   Mengembalikan jumlah slot kosong (`available`) dan total slot (`total`) per jenis kendaraan (`motor`, `mobil`, `truk`) untuk ditampilkan secara real-time.
2. **`GET /api/rates`**:
   Mengembalikan daftar tarif parkir aktif beserta denda tiket hilang untuk transparansi informasi publik.
3. **`POST /api/members`**:
   Menerima data pendaftaran member baru dari antarmuka web publik dengan status awal `pending` (memerlukan aktivasi manual oleh admin di dashboard internal).
