import { useEffect } from "react";

// ============================================================================
// KOMPONEN FEATURES (FASILITAS YANG KAMI TAWARKAN)
// ============================================================================
// Menampilkan 3 kartu keunggulan fasilitas parkir: Keamanan, Tarif, dan Akses Member.
export default function Features() {
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
    <section className="w-full bg-[#0f172a] py-16 px-6 flex justify-center">
      <div className="w-full max-w-7xl flex flex-col items-center">
        
        {/* Label Badge */}
        <div className="inline-flex items-center text-xs uppercase tracking-wider text-[#6D28D9] border border-[rgba(109,40,217,0.3)] bg-[rgba(109,40,217,0.1)] px-3 py-1 rounded-full mb-[0.75rem] font-semibold">
          KENAPA PARKIR DI SINI
        </div>

        {/* Heading */}
        <h2 className="text-2xl font-bold text-[#F8FAFC] text-center font-display">
          Fasilitas yang Kami Tawarkan
        </h2>

        {/* Sub-teks */}
        <p className="text-sm text-[#94A3B8] text-center mt-2 mb-[2.5rem] max-w-md">
          Dirancang untuk kenyamanan dan keamanan kendaraan kamu.
        </p>

        {/* Grid Kartu */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
          
          {/* Kartu 1: Aman & Terpantau */}
          <div className="bg-[rgba(109,40,217,0.08)] border-[0.5px] border-[rgba(109,40,217,0.2)] rounded-[10px] p-[1.25rem] text-center flex flex-col items-center justify-start">
            <div className="w-[44px] h-[44px] rounded-[10px] bg-[rgba(109,40,217,0.15)] flex items-center justify-center mx-auto mb-3">
              <i className="ti ti-shield-check text-[22px] text-[#6D28D9]"></i>
            </div>
            <h3 className="font-semibold text-[#F8FAFC] mb-1">Aman & Terpantau</h3>
            <p className="text-[#94A3B8] text-sm leading-[1.5]">
              CCTV 24 jam aktif di seluruh area parkir.
            </p>
          </div>

          {/* Kartu 2: Tarif Transparan */}
          <div className="bg-[rgba(245,158,11,0.07)] border-[0.5px] border-[rgba(245,158,11,0.2)] rounded-[10px] p-[1.25rem] text-center flex flex-col items-center justify-start">
            <div className="w-[44px] h-[44px] rounded-[10px] bg-[rgba(245,158,11,0.12)] flex items-center justify-center mx-auto mb-3">
              <i className="ti ti-receipt text-[22px] text-[#F59E0B]"></i>
            </div>
            <h3 className="font-semibold text-[#F8FAFC] mb-1">Tarif Transparan</h3>
            <p className="text-[#94A3B8] text-sm leading-[1.5]">
              Harga jelas sesuai durasi, tanpa biaya tersembunyi.
            </p>
          </div>

          {/* Kartu 3: Akses Member */}
          <div className="bg-[rgba(16,185,129,0.07)] border-[0.5px] border-[rgba(16,185,129,0.2)] rounded-[10px] p-[1.25rem] text-center flex flex-col items-center justify-start">
            <div className="w-[44px] h-[44px] rounded-[10px] bg-[rgba(16,185,129,0.12)] flex items-center justify-center mx-auto mb-3">
              <i className="ti ti-id-badge text-[22px] text-[#10B981]"></i>
            </div>
            <h3 className="font-semibold text-[#F8FAFC] mb-1">Akses Member</h3>
            <p className="text-[#94A3B8] text-sm leading-[1.5]">
              Daftar sekali, nikmati kemudahan parkir setiap saat.
            </p>
          </div>

        </div>

      </div>
    </section>
  );
}
