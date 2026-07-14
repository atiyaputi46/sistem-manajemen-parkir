import { Sparkles } from "lucide-react";
// Mengimpor gambar latar belakang gedung parkir
import heroBg from "../assets/images/parking_hero_bg_1782802486610.jpg";
import Section from "./Section";
import Card from "./Card";

// ============================================================================
// KOMPONEN HERO (BANNER UTAMA)
// ============================================================================
// Bagian pertama halaman web yang memberikan gambaran ringkas tentang aplikasi,
// tombol aksi cepat (CTA), dan dekorasi estetis pintu gerbang palang parkir.
export default function Hero() {
  


  return (
    <Section>
      <Card className="p-6 sm:p-8 md:p-10 flex flex-col justify-center min-h-[600px]">
        
        {/* 
          1. Gambar Latar Belakang & Overlay Gradasi Gelap
          Menggunakan filter opacity dan scale transition saat di-hover untuk 
          memberikan kedalaman visual tanpa mengaburkan teks di depannya.
        */}
        <div className="absolute inset-0 w-full h-full z-0 overflow-hidden">
          <img
            src={heroBg}
            alt="Parking Garage Background"
            className="w-full h-full object-cover object-left opacity-35 sm:opacity-45 group-hover:scale-105 transition-transform duration-700"
            referrerPolicy="no-referrer"
          />
          {/* Lapisan gradasi gelap agar teks putih mudah dibaca */}
          <div className="absolute inset-0 bg-gradient-to-r from-[#0F172A] via-[#0F172A]/80 to-transparent" />
        </div>

        {/* 
          2. Efek Cahaya Pendar Atmosferik (Atmospheric Light Glow)
          Div absolut dengan blur besar (blur-[100px]) berwarna biru langit 
          untuk memberikan estetika UI modern premium (*glassmorphism*).
        */}
        <div className="absolute top-0 right-0 w-80 h-80 bg-sky-500/10 rounded-full blur-[100px] -translate-y-12 translate-x-12 pointer-events-none z-0" />
        <div className="absolute -left-20 bottom-10 w-72 h-72 bg-sky-500/5 rounded-full blur-[90px] pointer-events-none z-0" />
  
        {/* 
          3. Konten Utama Hero
          Teks judul, deskripsi, dan tombol CTA diposisikan di atas (z-10).
        */}
        <div className="relative z-10 flex flex-col items-start text-left">
          
          {/* Label Tagline Kecil dengan Ikon Bintang */}
          <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-500/10 border border-sky-500/20 rounded-full mb-6">
            <Sparkles className="w-3.5 h-3.5 text-sky-400" />
            <span className="font-sans text-[10px] font-bold uppercase tracking-wider text-sky-400">
              Sistem Manajemen Parkir
            </span>
          </div>
  
          {/* Judul Utama (H1) */}
          <h1 className="font-display font-bold text-3xl sm:text-4xl lg:text-5xl text-white tracking-tight leading-tight mb-4">
            Parkir Lebih Mudah,<br />
            Terpantau Setiap Saat.
          </h1>
  
          {/* Deskripsi Singkat */}
          <p className="font-sans text-sm text-slate-300 max-w-lg mb-8 leading-relaxed">
            Temukan slot tersedia, cek tarif terkini, dan daftar sebagai member — semua dalam satu halaman terintegrasi.
          </p>
  

        </div>
  
        {/* 
          4. Elemen Dekorasi SVG Palang Gerbang Parkir (Boom Barrier)
          Tampil semi-transparan di pojok kanan bawah, terpengaruh hover efek grup.
        */}
        <div className="absolute bottom-0 right-4 pointer-events-none w-48 sm:w-64 opacity-25 transition-opacity duration-300 group-hover:opacity-40">
          <svg
            className="w-full h-16 text-sky-400"
            fill="none"
            viewBox="0 0 320 110"
            xmlns="http://www.w3.org/2000/svg"
          >
            {/* Tanah */}
            <line x1="10" y1="100" x2="310" y2="100" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
            {/* Kabinet Pilar / Tiang Gerbang */}
            <rect x="70" y="40" width="24" height="60" rx="3" stroke="currentColor" strokeWidth="2" fill="#0F172A" />
            {/* Engsel Sendi Putar */}
            <circle cx="82" cy="52" r="6" stroke="currentColor" strokeWidth="2" fill="currentColor" />
            {/* Palang Gerbang (Posisi naik miring) */}
            <line x1="82" y1="52" x2="280" y2="25" stroke="currentColor" strokeWidth="4" strokeLinecap="round" />
            {/* Garis Aksen Oranye pada Palang */}
            <line x1="120" y1="47" x2="135" y2="45" stroke="#F59E0B" strokeWidth="4" />
            <line x1="170" y1="40" x2="185" y2="38" stroke="#F59E0B" strokeWidth="4" />
            <line x1="220" y1="32" x2="235" y2="30" stroke="#F59E0B" strokeWidth="4" />
          </svg>
        </div>
      </Card>
    </Section>
  );
}
