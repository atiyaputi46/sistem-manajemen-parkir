import { useState, useEffect, useCallback } from "react";
import { Bike, Car, Truck, RefreshCw, AlertCircle } from "lucide-react";
import { API_BASE } from "../config";
import Section from "./Section";
import Card from "./Card";

// ============================================================================
// INTERFACE SLOT DATA
// ============================================================================
interface SlotData {
  // Jenis kendaraan: motor, mobil, atau truk
  type: "motor" | "mobil" | "truk";
  // Nama label kendaraan yang ramah pengguna
  name: string;
  // Jumlah slot parkir kosong yang tersedia saat ini
  available: number;
  // Total kapasitas tempat parkir untuk kategori tersebut
  total: number;
}

// ============================================================================
// KOMPONEN SLOTAVAILABILITY (PEMANTAU SLOT REALTIME)
// ============================================================================
// Menampilkan sisa kapasitas parkir kendaraan secara real-time. Komponen ini
// melakukan polling setiap 30 detik ke server dan menampilkan bar kemajuan (*progress bar*).
export default function SlotAvailability() {
  // State untuk menyimpan daftar data slot kendaraan
  const [slots, setSlots] = useState<SlotData[]>([]);
  // State indikator loading saat memanggil API
  const [loading, setLoading] = useState(true);
  // State untuk menangkap pesan error jika pemanggilan API gagal
  const [error, setError] = useState<string | null>(null);
  // State hitung mundur detik (countdown) dari 30 hingga 1
  const [countdown, setCountdown] = useState(30);

  // Fungsi callback untuk mengambil data ketersediaan slot dari API
  const fetchSlots = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      // Melakukan HTTP Request ke endpoint available-slots
      const response = await fetch(`${API_BASE}/api/available-slots`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const rawData = await response.json();
      
      let parsedSlots: SlotData[] = [];
      
      // Mengambil properti data (Laravel Standard wrapper) jika tersedia, atau langsung datanya
      const data = rawData.data !== undefined ? rawData.data : rawData;
      
      // Menangani format respons berupa Array
      if (Array.isArray(data)) {
        parsedSlots = data.map((item: any) => ({
          type: (item.type || item.jenis || "").toLowerCase().includes("motor") ? "motor" :
                (item.type || item.jenis || "").toLowerCase().includes("truk") ? "truk" : "mobil",
          name: item.name || item.jenis || item.label || "Kendaraan",
          available: Number(item.available !== undefined ? item.available : item.tersedia || 0),
          total: Number(item.total !== undefined ? item.total : item.kapasitas || 10),
        }));
      } 
      // Menangani format respons berupa Object Keyed (misal: { motor: {...}, mobil: {...} })
      else if (typeof data === "object" && data !== null) {
        parsedSlots = Object.keys(data).map((key) => {
          const item = data[key];
          const typeStr = key.toLowerCase();
          const vehicleType = typeStr.includes("motor") ? "motor" :
                              typeStr.includes("truk") ? "truk" : "mobil";
          const vehicleName = typeStr === "motor" ? "Motor" :
                              typeStr === "truk" ? "Truk" : "Mobil";
          return {
            type: vehicleType,
            name: item.name || vehicleName,
            available: Number(item.available !== undefined ? item.available : item.tersedia || 0),
            total: Number(item.total !== undefined ? item.total : item.kapasitas || 10),
          };
        });
      }

      // Menjamin ketersediaan 3 kategori utama (Motor, Mobil, Truk) di UI
      const types = ["motor", "mobil", "truk"] as const;
      const typeNames = { motor: "Motor", mobil: "Mobil", truk: "Truk" };
      
      const completeSlots = types.map((t) => {
        const found = parsedSlots.find((s) => s.type === t);
        if (found) return found;
        // Fallback nilai 0 jika data tidak ditemukan dari respons API
        return {
          type: t,
          name: typeNames[t],
          available: 0,
          total: 0,
        };
      });

      setSlots(completeSlots);
      // Reset hitung mundur setelah data berhasil ter-update
      setCountdown(30);
    } catch (err: any) {
      console.error("Error fetching slots:", err);
      setError("Gagal memuat data. Coba lagi.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Effect: Menjalankan pemanggilan data awal dan memasang polling berkala (30 detik)
  useEffect(() => {
    fetchSlots();

    const interval = setInterval(() => {
      fetchSlots();
    }, 30000); // 30000ms = 30 detik

    return () => clearInterval(interval);
  }, [fetchSlots]);

  // Effect: Mengontrol jalannya hitung mundur (countdown timer) setiap 1 detik
  useEffect(() => {
    if (loading || error) return;

    const timer = setInterval(() => {
      setCountdown((prev) => (prev > 1 ? prev - 1 : 30));
    }, 1000);

    return () => clearInterval(timer);
  }, [loading, error]);

  // Fungsi pembantu untuk mencocokkan ikon Lucide berdasarkan jenis kendaraan
  const getIcon = (type: "motor" | "mobil" | "truk", className = "w-5 h-5 text-sky-400") => {
    switch (type) {
      case "motor":
        return <Bike className={className} />;
      case "truk":
        return <Truck className={className} />;
      default:
        return <Car className={className} />;
    }
  };

  return (
    <Section id="ketersediaan">
      <Card className="p-6 md:p-8 flex flex-col justify-between min-h-[600px]">
        {/* Garis batas gradien estetis di bagian bawah card */}
        <div className="absolute bottom-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-sky-500 to-transparent opacity-50" />
        
        {/* Bagian Header Seksi Ketersediaan */}
        <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8">
          <div>
            <h2 className="font-display font-bold text-3xl md:text-4xl text-white">
              Slot Tersedia
            </h2>
            <p className="font-sans text-sm text-white/60 mt-2">
              Diperbarui setiap 30 detik
            </p>
          </div>
          
          {/* Label LIVE Pulsing jika data berhasil terhubung dengan lancar */}
          {!loading && !error && (
            <div className="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-500/10 border border-sky-500/20 rounded-lg text-xs font-bold tracking-wider text-sky-400 uppercase animate-pulse self-start">
              LIVE
            </div>
          )}
        </div>

        {/* STATE LOADING: Menampilkan Kerangka Palsu (Skeleton Skeletons) saat memuat */}
        {loading && slots.length === 0 && (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 flex-grow items-stretch">
            {[1, 2, 3].map((i) => (
              <div key={i} className="flex flex-col justify-between gap-4 p-6 bg-slate-800 rounded-2xl animate-pulse h-full">
                <div className="h-6 bg-slate-700 rounded w-1/3" />
                <div className="flex-grow flex items-center justify-center">
                  <div className="w-24 h-24 bg-slate-700 rounded-full" />
                </div>
                <div className="space-y-3">
                  <div className="h-5 bg-slate-700 rounded w-1/2" />
                  <div className="h-3 bg-slate-700 rounded w-full" />
                </div>
              </div>
            ))}
          </div>
        )}

        {/* STATE ERROR: Menampilkan tombol coba lagi jika koneksi gagal */}
        {error && (
          <div className="bg-red-500/5 border border-red-500/20 rounded-2xl p-6 text-center flex-grow flex flex-col justify-center items-center gap-4">
            <AlertCircle className="w-8 h-8 text-red-500" />
            <p className="font-sans text-sm text-white/80 font-medium">{error}</p>
            <button
              onClick={fetchSlots}
              className="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-slate-950 text-sm font-semibold rounded-lg hover:bg-sky-400 transition-colors"
            >
              <RefreshCw className="w-4 h-4" />
              Coba Lagi
            </button>
          </div>
        )}

        {/* STATE DATA READY: Rendering visual kartu parkir */}
        {(!loading || slots.length > 0) && !error && (
          <div className="flex-grow flex flex-col justify-between gap-8">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
              {slots.map((slot) => {
                // Mengecek apakah kapasitas slot kosong telah habis
                const isFull = slot.available === 0;
                // Menghitung jumlah slot yang sudah ditempati / terisi
                const occupied = Math.max(0, slot.total - slot.available);
                // Menghitung persentase keterisian untuk progress bar
                const percentageFilled = slot.total > 0 ? (occupied / slot.total) * 100 : 0;

                return (
                  <div
                    key={slot.type}
                    className="flex flex-col justify-between gap-6 p-6 md:p-8 rounded-2xl bg-slate-800/40 border border-white/5 hover:border-sky-500/30 hover:bg-slate-800/60 transition-all duration-300 h-full group relative overflow-hidden"
                  >
                    {/* Atas: Nama Kategori */}
                    <div className="flex items-center justify-between relative z-10">
                      <span className="font-display font-bold text-xl text-white tracking-wide uppercase">
                        {slot.name}
                      </span>
                    </div>

                    {/* Tengah: Ikon Kendaraan Berukuran Besar */}
                    <div className="flex-grow flex items-center justify-center py-6 relative z-10">
                      <div className="p-6 bg-sky-500/5 rounded-full border border-sky-500/10 group-hover:scale-110 group-hover:bg-sky-500/10 transition-transform duration-500">
                        {getIcon(slot.type, "w-16 h-16 sm:w-20 sm:h-20 text-sky-400")}
                      </div>
                    </div>

                    {/* Bawah: Keterangan Angka Kapasitas & Progress Bar */}
                    <div className="relative z-10">
                      <div className="flex justify-between items-end mb-3">
                        <span className="text-xs text-white/60 uppercase tracking-wider">Ketersediaan</span>
                        <div className="flex items-baseline gap-2">
                          {isFull ? (
                            <div className="flex items-center gap-2">
                              <span className="font-mono font-bold text-2xl text-red-400">
                                0
                              </span>
                              <span className="text-white/40 text-xs">/ {slot.total}</span>
                              <span className="px-2 py-1 text-[10px] font-bold tracking-wider uppercase rounded bg-red-500/10 border border-red-500/20 text-red-400 ml-1">
                                Penuh
                              </span>
                            </div>
                          ) : (
                            <div className="flex items-baseline gap-1">
                              <span className="font-mono font-bold text-3xl text-sky-400 leading-none">
                                {slot.available}
                              </span>
                              <span className="text-sm text-white/40 font-normal">/ {slot.total}</span>
                            </div>
                          )}
                        </div>
                      </div>

                      {/* Batang Progress Bar (Warna merah jika penuh, biru langit jika tersedia) */}
                      <div className="h-2 w-full bg-slate-900 rounded-full overflow-hidden border border-white/5">
                        <div
                          className={`h-full rounded-full transition-all duration-1000 ease-out ${
                            isFull ? "bg-red-500" : "bg-sky-400"
                          }`}
                          style={{ width: `${percentageFilled}%` }}
                        />
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>

            {/* Bagian Kaki Modul: Keterangan Polling & Angka Countdown Detik */}
            <div className="pt-6 border-t border-white/10 flex items-center justify-between text-xs text-white/40">
              <span className="flex items-center gap-2">
                <RefreshCw className="w-4 h-4 animate-spin text-sky-400" style={{ animationDuration: "4s" }} />
                <span>Memperbarui data otomatis</span>
              </span>
              <span className="text-sky-400 font-mono text-sm">{countdown} dtk</span>
            </div>
          </div>
        )}
      </Card>
    </Section>
  );
}
