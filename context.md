# Konteks Proyek: Sistem Parkir (Landing Page & Portal Portal Member)

Dokumen ini menjelaskan latar belakang, arsitektur teknis, dan integrasi API dari aplikasi **Sistem Parkir** untuk membantu memahami alur kerja dan pengembangan sistem ini secara menyeluruh.

---

## 1. Deskripsi Proyek
**Sistem Parkir** adalah sebuah aplikasi web responsif dan interaktif yang berfungsi sebagai portal informasi bagi pengunjung umum dan calon member parkir. Aplikasi ini dirancang dengan estetika modern menggunakan tema gelap (*dark mode*) premium, efek garis aksen dinamis, serta transisi dan animasi yang interaktif (menggunakan GSAP dan Motion).

Tujuan utama dari aplikasi ini adalah:
1. **Transparansi Informasi**: Menyajikan informasi ketersediaan slot kendaraan secara real-time dan rincian tarif parkir ter-update.
2. **Kemudahan Layanan**: Menyediakan formulir pendaftaran mandiri bagi pengendara yang ingin mendaftar sebagai member prioritas untuk mendapatkan akses keluar-masuk otomatis.

---

## 2. Arsitektur Teknis (Stack Teknologi)
Aplikasi ini dikembangkan dengan arsitektur terpisah antara frontend dan backend:

### Frontend
- **Framework & Core**: [React 19](https://react.dev/) dengan [TypeScript](https://www.typescriptlang.org/) untuk kode yang terstruktur dan aman.
- **Styling**: [Tailwind CSS](https://tailwindcss.com/) dengan modul integrasi Vite (`@tailwindcss/vite`) untuk pembuatan tata letak responsif dan desain visual premium.
- **Animasi & Efek**:
  - [GSAP (GreenSock Animation Platform)](https://gsap.com/) bersama dengan plugin `SplitText`, `CustomEase`, dan `ScrollTrigger` untuk efek preloader dan reveal sinematik di komponen `DorianReveal`.
  - [Motion (Framer Motion)](https://motion.dev/) untuk micro-interactions.
- **Ikon**: [Lucide React](https://lucide.dev/) untuk visualisasi representatif.

### Backend & API (Diasumsikan)
- **Backend Engine**: **Laravel** (berjalan secara lokal di `http://localhost:8000` saat development).
- **Vite Proxy Configuration**: Menggunakan proxy `/api` pada [vite.config.ts](file:///d:/sistem-parkir/vite.config.ts) agar request API dari frontend dikirim relatif ke origin yang sama guna menghindari kendala CORS.

---

## 3. Integrasi & Struktur API
Aplikasi frontend berinteraksi dengan tiga endpoint API utama yang disediakan oleh backend:

### A. Ketersediaan Slot (`GET /api/available-slots`)
- **Tujuan**: Mengambil sisa kapasitas parkir untuk tipe kendaraan **Motor**, **Mobil**, dan **Truk**.
- **Komponen**: [SlotAvailability.tsx](file:///d:/sistem-parkir/src/components/SlotAvailability.tsx)
- **Mekanisme**: Polling otomatis setiap **30 detik** dengan indikator live countdown timer untuk memberikan umpan balik visual kepada pengguna.
- **Resiliensi Data**: Parser pada frontend dirancang adaptif untuk mendukung format array objek umum maupun objek dengan keyed properties dari backend.

### B. Tarif Parkir (`GET /api/rates`)
- **Tujuan**: Menampilkan informasi biaya parkir terkini.
- **Komponen**: [Rates.tsx](file:///d:/sistem-parkir/src/components/Rates.tsx)
- **Data yang Ditampilkan**: Tarif jam pertama, tarif jam berikutnya, batas tarif maksimal harian (jika ada), serta denda untuk karcis yang hilang.

### C. Pendaftaran Member (`POST /api/members`)
- **Tujuan**: Mengirimkan data pendaftaran member baru ke server.
- **Komponen**: [MemberForm.tsx](file:///d:/sistem-parkir/src/components/MemberForm.tsx)
- **Bilingual Payload Mapping**: Mengirimkan parameter dalam bahasa Indonesia dan Inggris (misal: `plat_nomor` dan `license_plate`) agar kompatibel dengan skema database Laravel.
- **Penanganan Validasi**: Menangkap error validasi dari server (HTTP status `422` dari Laravel) dan memetakan pesan error secara spesifik ke masing-masing input form (Nama, Plat Nomor, Jenis Kendaraan, dan No HP).

---

## 4. Struktur Direktori Utama
Berikut adalah struktur folder penting pada aplikasi:
```bash
d:\sistem-parkir\
├── src/
│   ├── assets/              # Aset gambar, ilustrasi, dan icon
│   ├── components/          # Komponen UI modular
│   │   ├── Navbar.tsx       # Navigasi sticky dengan scroll smooth
│   │   ├── Hero.tsx         # Bagian utama penarik perhatian & deskripsi sistem
│   │   ├── SlotAvailability.tsx # Pemantau kapasitas slot parkir realtime (30s polling)
│   │   ├── Rates.tsx        # Panel informasi biaya dan denda parkir
│   │   ├── MemberForm.tsx   # Formulir registrasi member dengan validasi client-server
│   │   ├── DorianReveal.tsx # Intro & preloader animasi menggunakan GSAP
│   │   ├── Card.tsx         # Pembungkus layout bergaya Bento Grid
│   │   └── Section.tsx      # Kontainer grid halaman web
│   ├── App.tsx              # Komponen utama penyusun tata letak landing page
│   ├── config.ts            # Konfigurasi basis URL API
│   └── main.tsx             # Entrypoint inisialisasi React
├── package.json             # Manajer dependensi npm
└── vite.config.ts           # Konfigurasi build dan server development proxy
```
