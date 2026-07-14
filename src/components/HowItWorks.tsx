import { useEffect } from "react";

// ============================================================================
// KOMPONEN HOWITWORKS (CARA PARKIR DI SINI)
// ============================================================================
// Menampilkan 3 langkah alur proses parkir kendaraan lengkap dengan garis penghubung.
export default function HowItWorks() {
  // Menginjeksi CDN Tabler Icons secara dinamis jika belum ada di dalam DOM
  useEffect(() => {
    const linkId = "tabler-icons-cdn";
    if (!document.getElementById(linkId)) {
      const link = document.createElement("link");
      link.id = linkId;
      link.rel = "stylesheet";
      link.href = "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css";
      document.head.appendChild(link);
    }
  }, []);

  return (
    <section className="w-full bg-[#0B1120] py-16 px-6 flex justify-center">
      <div className="w-full max-w-7xl flex flex-col items-center">
        
        {/* Label Badge */}
        <div className="inline-flex items-center text-xs uppercase tracking-wider text-[#F59E0B] border border-[rgba(245,158,11,0.3)] bg-[rgba(245,158,11,0.1)] px-3 py-1 rounded-full mb-[0.75rem] font-semibold">
          PANDUAN SINGKAT
        </div>

        {/* Heading */}
        <h2 className="text-2xl font-bold text-[#F8FAFC] text-center font-display">
          Cara Parkir di Sini
        </h2>

        {/* Sub-teks */}
        <p className="text-sm text-[#94A3B8] text-center mt-2 mb-[2.5rem] max-w-md">
          Proses cepat, mudah, dan tidak ribet.
        </p>

        {/* Grid Alur */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 w-full relative">
          
          {/* Langkah 1 */}
          <div className="text-center p-4 relative flex flex-col items-center">
            {/* Garis penghubung desktop (ke kanan) */}
            <div className="absolute top-[38px] left-[calc(50%+22px)] right-0 h-[1.5px] bg-[rgba(109,40,217,0.3)] hidden md:block" />
            
            {/* Lingkaran Nomor */}
            <div className="w-11 h-11 rounded-full bg-[rgba(109,40,217,0.15)] border-[1.5px] border-[#6D28D9] flex items-center justify-center mx-auto mb-3 relative z-10 text-[#F8FAFC] font-semibold font-display">
              1
            </div>
            
            {/* Ikon */}
            <i className="ti ti-car-garage text-[#F59E0B] text-[24px] mb-1"></i>
            
            {/* Judul */}
            <h3 className="font-semibold text-[#F8FAFC] mb-1">Masuk & Ambil Karcis</h3>
            
            {/* Deskripsi */}
            <p className="text-[#94A3B8] text-sm leading-[1.5]">
              Petugas mencatat plat nomor dan memberikan karcis masuk.
            </p>
          </div>

          {/* Langkah 2 */}
          <div className="text-center p-4 relative flex flex-col items-center">
            {/* Garis penghubung desktop (kiri ke lingkaran + lingkaran ke kanan) */}
            <div className="absolute top-[38px] left-0 right-[calc(50%+22px)] h-[1.5px] bg-[rgba(109,40,217,0.3)] hidden md:block" />
            <div className="absolute top-[38px] left-[calc(50%+22px)] right-0 h-[1.5px] bg-[rgba(109,40,217,0.3)] hidden md:block" />
            
            {/* Lingkaran Nomor */}
            <div className="w-11 h-11 rounded-full bg-[rgba(109,40,217,0.15)] border-[1.5px] border-[#6D28D9] flex items-center justify-center mx-auto mb-3 relative z-10 text-[#F8FAFC] font-semibold font-display">
              2
            </div>
            
            {/* Ikon */}
            <i className="ti ti-parking text-[#F59E0B] text-[24px] mb-1"></i>
            
            {/* Judul */}
            <h3 className="font-semibold text-[#F8FAFC] mb-1">Parkir di Slot Tersedia</h3>
            
            {/* Deskripsi */}
            <p className="text-[#94A3B8] text-sm leading-[1.5]">
              Tempatkan kendaraan di slot yang ditunjuk petugas.
            </p>
          </div>

          {/* Langkah 3 */}
          <div className="text-center p-4 relative flex flex-col items-center">
            {/* Garis penghubung desktop (kiri ke lingkaran) */}
            <div className="absolute top-[38px] left-0 right-[calc(50%+22px)] h-[1.5px] bg-[rgba(109,40,217,0.3)] hidden md:block" />
            
            {/* Lingkaran Nomor */}
            <div className="w-11 h-11 rounded-full bg-[rgba(109,40,217,0.15)] border-[1.5px] border-[#6D28D9] flex items-center justify-center mx-auto mb-3 relative z-10 text-[#F8FAFC] font-semibold font-display">
              3
            </div>
            
            {/* Ikon */}
            <i className="ti ti-coin text-[#F59E0B] text-[24px] mb-1"></i>
            
            {/* Judul */}
            <h3 className="font-semibold text-[#F8FAFC] mb-1">Keluar & Bayar</h3>
            
            {/* Deskripsi */}
            <p className="text-[#94A3B8] text-sm leading-[1.5]">
              Tunjukkan karcis ke petugas, bayar sesuai durasi parkir.
            </p>
          </div>

        </div>

      </div>
    </section>
  );
}
