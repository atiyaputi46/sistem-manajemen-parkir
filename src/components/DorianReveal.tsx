import React from "react";
import motorcycleImg from "../assets/images/motorcycle.png";
import carImg from "../assets/images/car-white.png";
import trukImg from "../assets/images/truk.png";

// ============================================================================
// KOMPONEN DORIANREVEAL (MARQUEE SECTION)
// ============================================================================
// Menampilkan running text / marquee horizontal yang mengalir mulus tanpa jeda
// dengan menyandingkan teks branding dan ikon gambar kendaraan.
export default function DorianReveal() {
  return (
    <section className="w-full bg-[#0B1120] py-14 relative overflow-hidden">
      
      {/* Definisi Animasi CSS Marquee */}
      <style>{`
        @keyframes marquee {
          0% { transform: translateX(0); }
          100% { transform: translateX(-50%); }
        }
        .marquee-track {
          display: flex;
          width: max-content;
          animation: marquee 25s linear infinite;
          align-items: center;
        }
        .marquee-track:hover {
          animation-play-state: paused;
        }
      `}</style>

      {/* Label Atas */}
      <div className="text-center text-xs uppercase tracking-[0.2em] font-bold text-[#F59E0B] mb-8">
        SISTEM MANAJEMEN PARKIR
      </div>

      {/* Wrapper Marquee */}
      <div className="relative w-full overflow-hidden">
        
        {/* Fade Efek Gradasi Sisi Kiri */}
        <div className="absolute top-0 bottom-0 left-0 w-[150px] bg-gradient-to-r from-[#0B1120] to-transparent pointer-events-none z-10"></div>
        
        {/* Fade Efek Gradasi Sisi Kanan */}
        <div className="absolute top-0 bottom-0 right-0 w-[150px] bg-gradient-to-l from-[#0B1120] to-transparent pointer-events-none z-10"></div>

        {/* Track (Div Berjalan) */}
        <div className="marquee-track">
          
          {/* ==================== BLOK ITEM (SET 1) ==================== */}

          {/* Blok 1: Motor */}
          <div style={{ display: "flex", alignItems: "center", gap: "20px", padding: "0 48px" } as React.CSSProperties}>
            <div style={{ width: "60px", height: "60px", background: "rgba(109,40,217,0.15)", borderRadius: "50%", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 } as React.CSSProperties}>
              <img src={motorcycleImg} alt="Motor" style={{ height: "36px", objectFit: "contain", filter: "brightness(1.1)" } as React.CSSProperties} />
            </div>
            <span style={{ fontSize: "48px", fontWeight: 800, color: "#F8FAFC", whiteSpace: "nowrap", lineHeight: 1, letterSpacing: "-0.02em" } as React.CSSProperties}>
              PARKIR AMAN
            </span>
          </div>

          {/* Pemisah 1 */}
          <span style={{ color: "#F59E0B", fontSize: "36px", opacity: 0.5, flexShrink: 0 } as React.CSSProperties}>✦</span>

          {/* Blok 2: Mobil */}
          <div style={{ display: "flex", alignItems: "center", gap: "20px", padding: "0 48px" } as React.CSSProperties}>
            <div style={{ width: "60px", height: "60px", background: "rgba(245,158,11,0.12)", borderRadius: "50%", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 } as React.CSSProperties}>
              <img src={carImg} alt="Mobil" style={{ height: "36px", objectFit: "contain" } as React.CSSProperties} />
            </div>
            <span style={{ fontSize: "48px", fontWeight: 800, backgroundImage: "linear-gradient(90deg,#6D28D9,#F59E0B)", WebkitBackgroundClip: "text", WebkitTextFillColor: "transparent", whiteSpace: "nowrap", lineHeight: 1, letterSpacing: "-0.02em" } as React.CSSProperties}>
              TERINTEGRASI PENUH
            </span>
          </div>

          {/* Pemisah 2 */}
          <span style={{ color: "#6D28D9", fontSize: "36px", opacity: 0.5, flexShrink: 0 } as React.CSSProperties}>✦</span>

          {/* Blok 3: Truk */}
          <div style={{ display: "flex", alignItems: "center", gap: "20px", padding: "0 48px" } as React.CSSProperties}>
            <div style={{ width: "60px", height: "60px", background: "rgba(16,185,129,0.12)", borderRadius: "50%", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 } as React.CSSProperties}>
              <img src={trukImg} alt="Truk" style={{ height: "36px", objectFit: "contain" } as React.CSSProperties} />
            </div>
            <span style={{ fontSize: "48px", fontWeight: 800, color: "#F8FAFC", whiteSpace: "nowrap", lineHeight: 1, letterSpacing: "-0.02em" } as React.CSSProperties}>
              BAGI SEMUA MEMBER
            </span>
          </div>

          {/* Pemisah 3 */}
          <span style={{ color: "#F59E0B", fontSize: "36px", opacity: 0.5, flexShrink: 0 } as React.CSSProperties}>✦</span>


          {/* ==================== REPEAT BLOK ITEM (SET 2 - Seamless Loop) ==================== */}

          {/* Blok 1: Motor (Repeat) */}
          <div style={{ display: "flex", alignItems: "center", gap: "20px", padding: "0 48px" } as React.CSSProperties}>
            <div style={{ width: "60px", height: "60px", background: "rgba(109,40,217,0.15)", borderRadius: "50%", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 } as React.CSSProperties}>
              <img src={motorcycleImg} alt="Motor" style={{ height: "36px", objectFit: "contain", filter: "brightness(1.1)" } as React.CSSProperties} />
            </div>
            <span style={{ fontSize: "48px", fontWeight: 800, color: "#F8FAFC", whiteSpace: "nowrap", lineHeight: 1, letterSpacing: "-0.02em" } as React.CSSProperties}>
              PARKIR AMAN
            </span>
          </div>

          {/* Pemisah 1 (Repeat) */}
          <span style={{ color: "#F59E0B", fontSize: "36px", opacity: 0.5, flexShrink: 0 } as React.CSSProperties}>✦</span>

          {/* Blok 2: Mobil (Repeat) */}
          <div style={{ display: "flex", alignItems: "center", gap: "20px", padding: "0 48px" } as React.CSSProperties}>
            <div style={{ width: "60px", height: "60px", background: "rgba(245,158,11,0.12)", borderRadius: "50%", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 } as React.CSSProperties}>
              <img src={carImg} alt="Mobil" style={{ height: "36px", objectFit: "contain" } as React.CSSProperties} />
            </div>
            <span style={{ fontSize: "48px", fontWeight: 800, backgroundImage: "linear-gradient(90deg,#6D28D9,#F59E0B)", WebkitBackgroundClip: "text", WebkitTextFillColor: "transparent", whiteSpace: "nowrap", lineHeight: 1, letterSpacing: "-0.02em" } as React.CSSProperties}>
              TERINTEGRASI PENUH
            </span>
          </div>

          {/* Pemisah 2 (Repeat) */}
          <span style={{ color: "#6D28D9", fontSize: "36px", opacity: 0.5, flexShrink: 0 } as React.CSSProperties}>✦</span>

          {/* Blok 3: Truk (Repeat) */}
          <div style={{ display: "flex", alignItems: "center", gap: "20px", padding: "0 48px" } as React.CSSProperties}>
            <div style={{ width: "60px", height: "60px", background: "rgba(16,185,129,0.12)", borderRadius: "50%", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 } as React.CSSProperties}>
              <img src={trukImg} alt="Truk" style={{ height: "36px", objectFit: "contain" } as React.CSSProperties} />
            </div>
            <span style={{ fontSize: "48px", fontWeight: 800, color: "#F8FAFC", whiteSpace: "nowrap", lineHeight: 1, letterSpacing: "-0.02em" } as React.CSSProperties}>
              BAGI SEMUA MEMBER
            </span>
          </div>

          {/* Pemisah 3 (Repeat) */}
          <span style={{ color: "#F59E0B", fontSize: "36px", opacity: 0.5, flexShrink: 0 } as React.CSSProperties}>✦</span>

        </div>
      </div>
    </section>
  );
}
