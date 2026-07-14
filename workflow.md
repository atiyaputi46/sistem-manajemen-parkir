# Alur Kerja (Workflow) Sistem Parkir Berdasarkan Peran (Role)

Dokumen ini memetakan alur kerja pengguna dan sistem operasional parkir berdasarkan masing-masing peran (*role*). Pemahaman alur kerja ini berguna bagi tim pengembang maupun pengguna operasional dalam mengelola ekosistem parkir.

---

## 1. Pemetaan Peran (Role Mapping)
Sistem manajemen parkir ini membagi pengguna ke dalam 4 peran utama:
1. **Pengendara / Pengunjung Umum (Guest/Driver)**: Pengguna harian yang memerlukan informasi slot kosong dan tarif.
2. **Calon Member / Pendaftar (Member Applicant)**: Pengguna yang ingin meningkatkan akses mereka menjadi prioritas.
3. **Staf Admin Gerbang / Loket (Gate Administrator)**: Petugas gardu parkir yang memverifikasi dan mengaktifkan kartu member.
4. **Manajer Parkir / Admin Sistem (System/Parking Manager)**: Pengelola sistem keseluruhan yang mengatur konfigurasi operasional, tarif, dan pemantauan data.

---

## 2. Alur Kerja per Peran

### 1. Peran: Pengendara / Pengunjung Umum (Guest/Driver)
**Tujuan**: Mengetahui kondisi kapasitas parkir di lokasi tujuan dan biaya yang diperlukan secara transparan.

```mermaid
graph TD
    A[Buka Landing Page Sistem Parkir] --> B{Lihat Slot Tersedia?}
    B -->|Ya| C[Periksa slot Motor/Mobil/Truk secara Realtime]
    B -->|Tidak| D[Tunggu Refresh Polling 30 Detik]
    C --> E[Scroll ke bagian Tarif Parkir]
    E --> F[Pahami biaya Jam Pertama, Berikutnya, & Denda Tiket Hilang]
    F --> G[Tiba di Lokasi & Lakukan Parkir Fisik]
```

- **Langkah 1**: Pengendara mengakses landing page melalui peramban (smartphone/desktop).
- **Langkah 2**: Memantau modul **Ketersediaan Slot** (Motor, Mobil, Truk) yang diperbarui setiap 30 detik untuk memastikan ketersediaan tempat parkir sebelum berangkat/masuk.
- **Langkah 3**: Memeriksa modul **Tarif Parkir** untuk mengestimasi biaya parkir berdasarkan durasi kunjungan mereka.
- **Langkah 4**: Memarkir kendaraan di lokasi menggunakan tiket harian konvensional (mengambil tiket fisik di dispenser gerbang masuk).

---

### 2. Peran: Calon Member / Pendaftar (Member Applicant)
**Tujuan**: Melakukan pendaftaran akses member prioritas agar bisa keluar-masuk otomatis tanpa perlu antre tiket harian dan bayar tunai.

```mermaid
graph TD
    A[Buka Modul Akses Prioritas Member] --> B[Isi Formulir Pendaftaran]
    B --> C[Isi Nama, Plat Kendaraan, Jenis Kendaraan, & No HP]
    C --> D[Klik Kirim Pendaftaran]
    D --> E{Validasi Input?}
    E -->|Gagal| F[Perbaiki Input sesuai Petunjuk Error]
    E -->|Sukses| G[Terkirim ke API /api/members]
    G --> H[Muncul Layar Sukses & Tunggu Dihubungi Admin]
```

- **Langkah 1**: Pengendara menavigasi ke bagian **Daftar Member** di aplikasi.
- **Langkah 2**: Mengisi data formulir pendaftaran:
  - **Nama Lengkap** (untuk pencocokan identitas kartu).
  - **Plat Kendaraan** (untuk sistem pemindaian plat nomor otomatis / ANPR).
  - **Jenis Kendaraan** (pilihan: Motor, Mobil, Truk - untuk penyesuaian slot & tarif member).
  - **WhatsApp / HP** (untuk media komunikasi aktivasi).
- **Langkah 3**: Mengirimkan form. Sistem melakukan validasi lokal (client-side) dan kemudian mengirimkan data ke database via API `POST /api/members`.
- **Langkah 4**: Jika berhasil, pendaftar melihat pesan sukses dan menunggu proses aktivasi lebih lanjut dari Admin.

---

### 3. Peran: Staf Admin Gerbang / Loket (Gate Administrator)
**Tujuan**: Memproses dan memvalidasi pendaftaran member baru serta mengaktifkan kartu akses fisik RFID/e-money mereka di gerbang.

```mermaid
graph TD
    A[Pantau Registrasi Baru di Backend] --> B[Hubungi Calon Member via WA/HP]
    B --> C[Calon Member Datang ke Loket Gerbang]
    C --> D[Verifikasi Fisik Kendaraan & Identitas]
    D --> E[Tautkan Kartu RFID/E-Money ke Akun Member]
    E --> F[Aktivasi Status Member di Database]
    F --> G[Member Dapat Keluar-Masuk Otomatis]
```

- **Langkah 1**: Admin memantau pendaftaran masuk melalui dashboard backend admin Laravel.
- **Langkah 2**: Menghubungi calon member via WhatsApp atau HP untuk konfirmasi berkas atau mengundang mereka ke loket gerbang parkir.
- **Langkah 3**: Ketika member datang ke loket gerbang membawa kartu fisik (RFID/e-money) dan kendaraannya:
  - Melakukan pencocokan plat nomor fisik dengan data pendaftaran.
  - Melakukan *tap* kartu pada pembaca kartu (*card reader*) di loket untuk mendapatkan nomor UID kartu.
- **Langkah 4**: Admin mendaftarkan nomor UID kartu ke akun member di sistem gerbang.
- **Langkah 5**: Mengubah status pendaftaran member menjadi **Aktif** di database.

---

### 4. Peran: Manajer Parkir / Admin Sistem (System/Parking Manager)
**Tujuan**: Mengelola konfigurasi operasional, tarif, dan memantau keseluruhan aktivitas bisnis perparkiran.

```mermaid
graph TD
    A[Akses Dashboard Sistem Manajemen] --> B{Pilih Tugas}
    B -->|Kelola Tarif| C[Update Tarif Baru di Database]
    B -->|Pantau Slot| D[Lihat Okupansi & Kapasitas Maksimal]
    B -->|Kelola Member| E[Lihat Laporan Transaksi & Status Member]
    C --> F[Perubahan Otomatis Tayang di Landing Page]
```

- **Alur Pengelolaan Tarif**:
  1. Manajer memutuskan penyesuaian tarif berdasarkan regulasi atau hari libur.
  2. Memperbarui nominal tarif (jam pertama, jam berikutnya, tarif maksimal harian, denda tiket hilang) melalui panel admin backend Laravel.
  3. API `/api/rates` memperbarui datanya secara real-time, dan tampilan di landing page pengunjung langsung menyesuaikan tanpa perlu dideploy ulang.
- **Alur Pemantauan Slot**:
  1. Manajer mengawasi data okupansi slot untuk merencanakan perluasan area parkir atau pemeliharaan slot.
  2. Jika terjadi sensor rusak, manajer dapat mengesampingkan (*override*) jumlah slot tersedia di database agar data yang tampil di landing page tetap akurat bagi pengunjung.
- **Alur Pengawasan Member**:
  1. Memantau keaktifan kartu member, mendeteksi penyalahgunaan kartu (seperti kartu yang digunakan bergantian oleh kendaraan berbeda), serta menarik laporan pendapatan biaya berlangganan member bulanan.
