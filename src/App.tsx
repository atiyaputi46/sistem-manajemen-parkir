// Import seluruh komponen penyusun halaman web Sistem Parkir
import Navbar from "./components/Navbar";
import Hero from "./components/Hero";
import Features from "./components/Features";
import HowItWorks from "./components/HowItWorks";
import SlotAvailability from "./components/SlotAvailability";
import Rates from "./components/Rates";
import MemberForm from "./components/MemberForm";
import DorianReveal from "./components/DorianReveal";
import Footer from "./components/Footer";

// ============================================================================
// KOMPONEN UTAMA (APP)
// ============================================================================
// Mengatur tata letak halaman utama secara vertikal dengan latar belakang gelap 
// yang mewah (#0B1220) serta aksen garis dekorasi grid global.
export default function App() {
  return (
    <div className="min-h-screen bg-[#0B1220] text-slate-300 selection:bg-sky-500/20 selection:text-sky-300 overflow-x-hidden antialiased flex flex-col justify-between relative">
      
      {/* 
        Garis Aksen Grid Latar Belakang Global (Dekoratif)
        Menggunakan SVG fixed penuh layar dengan opacity tipis (opacity-5) untuk 
        memberikan nuansa futuristik / teknologi modern.
      */}
      <div className="fixed inset-0 pointer-events-none opacity-5 overflow-hidden z-0">
        <svg className="w-full h-full" xmlns="http://www.w3.org/2000/svg">
          <line x1="10%" y1="0" x2="80%" y2="100%" stroke="white" strokeWidth="1" />
          <line x1="30%" y1="0" x2="95%" y2="100%" stroke="white" strokeWidth="1" />
          <line x1="0" y1="50%" x2="100%" y2="10%" stroke="white" strokeWidth="1" />
          <line x1="0%" y1="80%" x2="100%" y2="60%" stroke="white" strokeWidth="1" />
          <line x1="60%" y1="0" x2="20%" y2="100%" stroke="white" strokeWidth="1" />
        </svg>
      </div>

      {/* Kontainer Relatif Konten untuk menumpuk elemen di atas grid latar belakang */}
      <div className="relative z-10 flex flex-col justify-between min-h-screen">
        
        {/* 1. Bar Navigasi (Sticky di atas layar) */}
        <Navbar />

        {/* 2. Kontainer Utama Halaman - Ditumpuk Vertikal */}
        <main className="flex-grow w-full flex flex-col items-center pt-20 md:pt-24">
          
          {/* Bagian Banner Promosi & Pengenalan Utama */}
          <Hero />
          
          {/* Fasilitas yang Kami Tawarkan */}
          <Features />
          
          {/* Cara Parkir di Sini */}
          <HowItWorks />
          
          {/* Modul Informasi Ketersediaan Slot Parkir Real-Time */}
          <SlotAvailability />
          
          {/* Modul Tabel Informasi Tarif & Denda Terkini */}
          <Rates />
          
          {/* Modul Formulir Registrasi Akses Member Prioritas */}
          <MemberForm />
          
          {/* Bagian Animasi Reveal Sinematik (GSAP Showcase) */}
          <section className="w-full">
            <DorianReveal />
          </section>
          
        </main>

        {/* 3. Blok Informasi Kaki Halaman (Sitemap, Legal, Sosial Media) */}
        <Footer />
        
      </div>
    </div>
  );
}
