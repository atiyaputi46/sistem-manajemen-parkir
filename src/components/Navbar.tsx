import React, { useState, useEffect } from "react";
import { Menu, X, ParkingCircle } from "lucide-react";

// ============================================================================
// KOMPONEN NAVBAR (STICKY NAVIGATION)
// ============================================================================
// Menyediakan bar navigasi responsif yang melayang di atas halaman (sticky) dengan
// deteksi scroll untuk mengubah opacity latar belakang menjadi blur transparan.
export default function Navbar() {
  // State untuk mengontrol status buka/tutup menu hamburger pada layar mobile
  const [isOpen, setIsOpen] = useState(false);
  // State untuk melacak apakah halaman telah di-scroll lebih dari 50px
  const [isScrolled, setIsScrolled] = useState(false);

  // Effect untuk memasang event listener scroll pada objek window
  useEffect(() => {
    const handleScroll = () => {
      // Set state jika posisi scroll vertikal melampaui 50 pixel
      setIsScrolled(window.scrollY > 50);
    };
    
    // Menggunakan passive listener untuk performa scroll yang lebih baik
    window.addEventListener("scroll", handleScroll, { passive: true });
    
    // Membersihkan event listener saat komponen di-unmount
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  // Definisi tautan navigasi yang memetakan nama menu dan target selektor ID seksi
  const navLinks = [
    { name: "Ketersediaan", href: "#ketersediaan" },
    { name: "Tarif", href: "#tarif" },
  ];

  // Fungsi untuk menangani klik tautan menu agar melakukan scroll secara halus (smooth scroll)
  const handleNavClick = (e: React.MouseEvent<HTMLAnchorElement>, href: string) => {
    e.preventDefault();
    // Tutup menu mobile jika sedang terbuka
    setIsOpen(false);
    
    // Cari elemen berdasarkan query selector selector id
    const element = document.querySelector(href);
    if (element) {
      // Lakukan scroll dengan animasi smooth
      element.scrollIntoView({ behavior: "smooth" });
    }
  };

  return (
    <nav 
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled 
          ? "bg-[#0B1220]/70 backdrop-blur-md border-b border-white/10 shadow-lg shadow-black/20" 
          : "bg-transparent border-b border-transparent"
      }`}
    >
      <div className="max-w-7xl mx-auto px-6 md:px-8">
        <div className="flex items-center justify-between h-16">
          
          {/* Bagian Kiri: Identitas Brand dan Logo */}
          <div className="flex items-center gap-3">
            <div className="p-1.5 bg-sky-500/10 rounded-lg border border-sky-500/20">
              <ParkingCircle className="w-5 h-5 text-sky-400" />
            </div>
            <span className="font-display font-black text-lg text-white tracking-tight">
              Sistem Parkir
            </span>
          </div>

          {/* Bagian Kanan: Menu Tautan untuk Tampilan Desktop */}
          <div className="hidden md:flex items-center space-x-8">
            {navLinks.map((link) => (
              <a
                key={link.name}
                href={link.href}
                onClick={(e) => handleNavClick(e, link.href)}
                className="relative font-sans text-sm font-medium text-white/80 hover:text-white transition-colors duration-300 group"
              >
                {link.name}
                {/* Efek garis bawah dinamis saat di-hover */}
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-sky-400 transition-all duration-300 group-hover:w-full"></span>
              </a>
            ))}
            {/* Tombol Registrasi Utama di Desktop */}
            <a
              href="#daftar-member"
              onClick={(e) => handleNavClick(e, "#daftar-member")}
              className="font-sans text-sm font-bold px-5 py-2.5 bg-sky-500 text-slate-950 rounded-lg transition-all duration-300 hover:bg-sky-400 hover:-translate-y-0.5 shadow-lg shadow-sky-500/20 active:translate-y-0 cursor-pointer"
            >
              Daftar Member Sekarang
            </a>
          </div>

          {/* Tombol Hamburger Menu pada Tampilan Mobile */}
          <div className="md:hidden">
            <button
              onClick={() => setIsOpen(!isOpen)}
              type="button"
              className="p-2 rounded-md text-white/80 hover:text-white focus:outline-none"
              aria-label="Toggle menu"
            >
              {/* Render ikon silang jika menu terbuka, ikon hamburger jika tertutup */}
              {isOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>
      </div>

      {/* Dropdown Menu Tampilan Mobile */}
      <div
        className={`md:hidden overflow-hidden transition-all duration-300 ease-in-out ${
          isOpen ? "max-h-64 opacity-100" : "max-h-0 opacity-0"
        } bg-[#0F172A] border-b border-white/10`}
      >
        <div className="px-4 pt-2 pb-6 space-y-2">
          {navLinks.map((link) => (
            <a
              key={link.name}
              href={link.href}
              onClick={(e) => handleNavClick(e, link.href)}
              className="block px-3 py-2.5 rounded-lg text-base font-medium text-white/80 hover:text-white hover:bg-white/5 transition-all duration-200"
            >
              {link.name}
            </a>
          ))}
          {/* Tombol Registrasi Utama di Mobile */}
          <div className="px-3 pt-4">
            <a
              href="#daftar-member"
              onClick={(e) => handleNavClick(e, "#daftar-member")}
              className="block text-center px-4 py-3 bg-sky-500 text-slate-950 rounded-lg font-bold text-sm transition-all duration-300 hover:bg-sky-400 shadow-md cursor-pointer"
            >
              Daftar Sekarang
            </a>
          </div>
        </div>
      </div>
    </nav>
  );
}
