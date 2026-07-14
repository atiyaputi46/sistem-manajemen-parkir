import { useState } from "react";
import FooterModal from "./FooterModal";
import { ParkingCircle, Twitter, Linkedin, Instagram, Facebook, ArrowUp } from "lucide-react";
import Section from "./Section";
import Card from "./Card";

// ============================================================================
// KONSTANTA KONTEN MODAL (MODAL_CONTENT)
// ============================================================================
const MODAL_CONTENT = {
  privacy: {
    title: 'Kebijakan privasi',
    icon: 'ti ti-shield-lock',
    items: [
      {
        icon: 'ti ti-database',
        bgColor: 'var(--color-violet, #6D28D9)',
        bgOpacity: '15%',
        title: 'Data yang dikumpulkan',
        desc: 'Kami hanya menyimpan nama lengkap, nomor plat kendaraan, dan nomor HP yang kamu masukkan saat pendaftaran member.'
      },
      {
        icon: 'ti ti-lock',
        bgColor: '#10B981',
        bgOpacity: '15%',
        title: 'Keamanan data',
        desc: 'Data kamu tidak dibagikan kepada pihak ketiga dan hanya digunakan untuk keperluan sistem parkir ini.'
      },
      {
        icon: 'ti ti-user-x',
        bgColor: '#F59E0B',
        bgOpacity: '15%',
        title: 'Penghapusan data',
        desc: 'Kamu dapat meminta admin untuk menghapus data pendaftaran member kamu kapan saja.'
      }
    ]
  },
  terms: {
    title: 'Syarat & ketentuan',
    icon: 'ti ti-file-description',
    items: [
      {
        icon: 'ti ti-car',
        bgColor: '#EF4444',
        bgOpacity: '15%',
        title: 'Tanggung jawab kendaraan',
        desc: 'Pengelola parkir tidak bertanggung jawab atas kerusakan atau kehilangan barang di dalam kendaraan.'
      },
      {
        icon: 'ti ti-ticket',
        bgColor: 'var(--color-violet, #6D28D9)',
        bgOpacity: '15%',
        title: 'Karcis parkir',
        desc: 'Simpan karcis parkir dengan baik. Kehilangan karcis dikenakan denda sesuai tarif yang berlaku.'
      },
      {
        icon: 'ti ti-clock',
        bgColor: '#10B981',
        bgOpacity: '15%',
        title: 'Jam operasional',
        desc: 'Fasilitas parkir beroperasi sesuai jam yang ditetapkan. Kendaraan melebihi jam operasional dikenakan tarif harian.'
      }
    ]
  },
  help: {
    title: 'Bantuan teknis',
    icon: 'ti ti-headset',
    items: [
      {
        icon: 'ti ti-phone',
        bgColor: 'var(--color-violet, #6D28D9)',
        bgOpacity: '15%',
        title: 'Hubungi admin',
        desc: 'Untuk bantuan teknis sistem, hubungi administrator di loket utama atau melalui nomor yang tertera di papan informasi.'
      },
      {
        icon: 'ti ti-clock-hour-3',
        bgColor: '#10B981',
        bgOpacity: '15%',
        title: 'Jam layanan',
        desc: 'Layanan admin tersedia pada jam operasional parkir. Di luar jam tersebut, pesan akan direspons pada hari kerja berikutnya.'
      }
    ]
  }
};

// ============================================================================
// KOMPONEN FOOTER (KAKI HALAMAN WEB)
// ============================================================================
// Bagian kaki halaman web yang berisi deskripsi singkat sistem, tautan media sosial,
// navigasi cepat (sitemap), informasi hukum (legal), serta tombol untuk kembali ke atas.
export default function Footer() {
  const [activeModal, setActiveModal] = useState<"privacy" | "terms" | "help" | null>(null);
  
  // Fungsi untuk menggulirkan halaman web kembali ke posisi paling atas secara halus
  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  // Fungsi untuk menggulirkan halaman secara halus ke elemen seksi tertentu berdasarkan query selector ID
  const scrollToSection = (id: string) => {
    const element = document.querySelector(id);
    if (element) {
      element.scrollIntoView({ behavior: "smooth" });
    }
  };

  return (
    <footer className="w-full">
      <Section className="!pt-10 md:!pt-14 !pb-12">
        {/* Kontainer Utama Footer menggunakan komponen Card agar tampil senada dengan komponen lain */}
        <Card className="!bg-[#0B1220]">
          
          {/* Grid Layout pembagian kolom konten */}
          <div className="relative z-10 p-8 sm:p-12 md:p-16 grid grid-cols-1 md:grid-cols-12 gap-12">
            
            {/* Kolom Kiri (Lebar 6 Kolom): Brand, Deskripsi Proyek, Medsos, dan Tombol Kembali Ke Atas */}
            <div className="md:col-span-6 flex flex-col items-start justify-between space-y-8">
              <div className="space-y-4">
                {/* Logo Brand */}
                <div className="flex items-center gap-3">
                  <div className="p-1.5 bg-sky-500/10 rounded-lg border border-sky-500/20">
                    <ParkingCircle className="w-5 h-5 text-sky-400" />
                  </div>
                  <span className="font-display font-black text-lg tracking-wider text-white uppercase">
                    Sistem Parkir
                  </span>
                </div>

                {/* Deskripsi Proyek Singkat */}
                <p className="font-sans text-sm text-white/60 max-w-sm leading-relaxed">
                  Sistem manajemen parkir pintar berbasis web untuk efisiensi ketersediaan slot real-time, transparansi tarif, dan pendaftaran akses member prioritas secara instan.
                </p>
              </div>

              {/* Tautan Akun Media Sosial Resmi */}
              <div className="flex items-center gap-4 text-white/60">
                <a href="#" className="hover:text-white transition-colors p-1" aria-label="Twitter">
                  <Twitter className="w-5 h-5" />
                </a>
                <a href="#" className="hover:text-white transition-colors p-1" aria-label="LinkedIn">
                  <Linkedin className="w-5 h-5" />
                </a>
                <a href="#" className="hover:text-white transition-colors p-1" aria-label="Instagram">
                  <Instagram className="w-5 h-5" />
                </a>
                <a href="#" className="hover:text-white transition-colors p-1" aria-label="Facebook">
                  <Facebook className="w-5 h-5" />
                </a>
              </div>

              {/* Tombol Aksi Kembali ke Atas Halaman */}
              <button
                onClick={scrollToTop}
                className="inline-flex items-center gap-2 px-4 py-2 border border-white/10 hover:border-white/30 text-white/60 hover:text-white text-xs font-bold tracking-wider uppercase rounded-lg transition-all duration-300 bg-white/5 hover:bg-white/10 cursor-pointer"
              >
                <ArrowUp className="w-4 h-4" />
                Kembali Ke Atas
              </button>
            </div>

            {/* Kolom Tengah (Lebar 3 Kolom): Site Map Tautan Navigasi Cepat */}
            <div className="md:col-span-3 space-y-6">
              <h3 className="font-display font-bold text-sm tracking-wider text-white uppercase">
                Site Map
              </h3>
              <ul className="space-y-4 font-sans text-sm text-white/60">
                <li>
                  <button
                    onClick={scrollToTop}
                    className="hover:text-white hover:underline transition-colors text-left"
                  >
                    Homepage
                  </button>
                </li>
                <li>
                  <button
                    onClick={() => scrollToSection("#ketersediaan")}
                    className="hover:text-white hover:underline transition-colors text-left"
                  >
                    Ketersediaan Slot
                  </button>
                </li>
                <li>
                  <button
                    onClick={() => scrollToSection("#tarif")}
                    className="hover:text-white hover:underline transition-colors text-left"
                  >
                    Tarif Parkir
                  </button>
                </li>
                <li>
                  <button
                    onClick={() => scrollToSection("#daftar-member")}
                    className="hover:text-white hover:underline transition-colors text-left"
                  >
                    Daftar Member
                  </button>
                </li>
              </ul>
            </div>

            {/* Kolom Kanan (Lebar 3 Kolom): Tautan Legalitas & Hukum */}
            <div className="md:col-span-3 space-y-6">
              <h3 className="font-display font-bold text-sm tracking-wider text-white uppercase">
                Legal
              </h3>
              <ul className="space-y-4 font-sans text-sm text-white/60">
                <li>
                  <button
                    onClick={() => setActiveModal('privacy')}
                    className="hover:text-white hover:underline transition-colors block text-left w-full cursor-pointer"
                  >
                    Kebijakan Privasi
                  </button>
                </li>
                <li>
                  <button
                    onClick={() => setActiveModal('terms')}
                    className="hover:text-white hover:underline transition-colors block text-left w-full cursor-pointer"
                  >
                    Syarat & Ketentuan
                  </button>
                </li>
                <li>
                  <button
                    onClick={() => setActiveModal('help')}
                    className="hover:text-white hover:underline transition-colors block text-left w-full cursor-pointer"
                  >
                    Bantuan Teknis
                  </button>
                </li>
              </ul>
            </div>

          </div>

          {/* Bagian Copyright Kaki Paling Bawah */}
          <div className="bg-sky-500 py-4 px-6 text-center text-slate-950 text-xs font-bold tracking-wider uppercase rounded-b-2xl border-t border-sky-500/10">
            Copyright &copy; {new Date().getFullYear()} Sistem Parkir Realtime. All Rights Reserved.
          </div>

        </Card>
      </Section>
      
      {/* Modal Popup Kebijakan Privasi, Syarat & Ketentuan, dan Bantuan Teknis */}
      <FooterModal
        isOpen={activeModal === 'privacy'}
        onClose={() => setActiveModal(null)}
        title={MODAL_CONTENT.privacy.title}
        icon={MODAL_CONTENT.privacy.icon}
        items={MODAL_CONTENT.privacy.items}
      />
      <FooterModal
        isOpen={activeModal === 'terms'}
        onClose={() => setActiveModal(null)}
        title={MODAL_CONTENT.terms.title}
        icon={MODAL_CONTENT.terms.icon}
        items={MODAL_CONTENT.terms.items}
      />
      <FooterModal
        isOpen={activeModal === 'help'}
        onClose={() => setActiveModal(null)}
        title={MODAL_CONTENT.help.title}
        icon={MODAL_CONTENT.help.icon}
        items={MODAL_CONTENT.help.items}
      />
    </footer>
  );
}

