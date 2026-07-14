import { useState, useEffect, useCallback } from "react";
import { Bike, Car, Truck, Coins, AlertCircle, RefreshCw } from "lucide-react";
import { API_BASE } from "../config";
import Section from "./Section";
import Card from "./Card";

// ============================================================================
// INTERFACE RATE DATA
// ============================================================================
interface RateData {
  // Jenis kendaraan: motor, mobil, atau truk
  type: "motor" | "mobil" | "truk";
  // Nama label kendaraan yang ramah pengguna
  name: string;
  // Tarif parkir untuk 1 jam pertama
  first_hour_rate: number;
  // Tarif parkir untuk setiap jam berikutnya
  subsequent_hour_rate: number;
  // Batas tarif maksimal harian (null berarti tidak terbatas)
  daily_max_rate: number | null;
  // Jumlah denda jika karcis/tiket parkir hilang
  fine_lost_ticket: number;
}

// ============================================================================
// KOMPONEN RATES (INFORMASI TARIF PARKIR)
// ============================================================================
// Menampilkan daftar tarif parkir kendaraan berdasarkan jenisnya dengan data
// yang terintegrasi secara dinamis ke backend API.
export default function Rates() {
  // State untuk menyimpan data daftar tarif
  const [rates, setRates] = useState<RateData[]>([]);
  // State indikator memuat data
  const [loading, setLoading] = useState(true);
  // State penyimpan pesan error
  const [error, setError] = useState<string | null>(null);

  // Fungsi pembantu untuk memformat nominal angka menjadi format mata uang Rupiah (IDR)
  const formatRupiah = (value: number | null | undefined) => {
    if (value === null || value === undefined) return "Tidak ada batas";
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    }).format(value);
  };

  // Fungsi callback untuk mengambil data tarif dari server
  const fetchRates = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await fetch(`${API_BASE}/api/rates`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const rawData = await response.json();
      
      const data = rawData.data !== undefined ? rawData.data : rawData;
      let parsedRates: RateData[] = [];

      // Memetakan isi data array dari API secara aman agar fleksibel mendukung format bahasa Inggris/Indonesia
      if (Array.isArray(data)) {
        parsedRates = data.map((item: any) => ({
          type: (item.vehicle_type || item.type || item.jenis || "").toLowerCase().includes("motor") ? "motor" :
                (item.vehicle_type || item.type || item.jenis || "").toLowerCase().includes("truk") ? "truk" : "mobil",
          name: item.name || item.jenis || item.label || (item.vehicle_type === 'motor' ? 'Motor' : item.vehicle_type === 'truk' ? 'Truk' : 'Kendaraan'),
          first_hour_rate: Number(item.first_hour_rate !== undefined ? item.first_hour_rate : item.tarif_awal || 0),
          subsequent_hour_rate: Number(item.subsequent_hour_rate !== undefined ? item.subsequent_hour_rate : item.tarif_berikutnya || 0),
          daily_max_rate: item.daily_max_rate !== undefined ? (item.daily_max_rate !== null ? Number(item.daily_max_rate) : null) :
                          (item.tarif_maksimal !== undefined ? (item.tarif_maksimal !== null ? Number(item.tarif_maksimal) : null) : null),
          fine_lost_ticket: Number(item.fine_lost_ticket !== undefined ? item.fine_lost_ticket : item.denda_hilang || 0),
        }));
      }

      // Menjamin ketersediaan 3 kategori data tarif di UI (Motor, Mobil, Truk)
      const types = ["motor", "mobil", "truk"] as const;
      const typeNames = { motor: "Motor", mobil: "Mobil", truk: "Truk" };

      const completeRates = types.map((t) => {
        const found = parsedRates.find((r) => r.type === t);
        if (found) return found;
        return {
          type: t,
          name: typeNames[t],
          first_hour_rate: 0,
          subsequent_hour_rate: 0,
          daily_max_rate: null,
          fine_lost_ticket: 0,
        };
      });

      setRates(completeRates);
    } catch (err: any) {
      console.error("Error fetching rates:", err);
      setError("Gagal memuat data tarif. Coba lagi.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Effect untuk mengeksekusi fetch data sekali pada saat komponen dipasang
  useEffect(() => {
    fetchRates();
  }, [fetchRates]);

  // Fungsi pembantu untuk merender ikon kendaraan Lucide berdasarkan tipe
  const getIcon = (type: "motor" | "mobil" | "truk") => {
    switch (type) {
      case "motor":
        return <Bike className="w-4 h-4 text-sky-400" />;
      case "truk":
        return <Truck className="w-4 h-4 text-sky-400" />;
      default:
        return <Car className="w-4 h-4 text-sky-400" />;
    }
  };

  return (
    <Section id="tarif">
      <Card className="p-6 md:p-8 flex flex-col justify-between min-h-[600px]">
        {/* Garis batas gradien dekoratif di bagian bawah card */}
        <div className="absolute bottom-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-sky-500 to-transparent opacity-50" />

        {/* Bagian Header Seksi Tarif */}
        <div className="mb-8">
          <h2 className="font-display font-bold text-3xl md:text-4xl text-white">
            Tarif Parkir Terkini
          </h2>
          <p className="font-sans text-sm text-white/60 mt-2">
            Tarif dihitung per jam. Berlaku untuk semua pintu masuk.
          </p>
        </div>

        {/* STATE LOADING: Menampilkan skeleton loader */}
        {loading && rates.length === 0 && (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 flex-grow items-stretch">
            {[1, 2, 3].map((i) => (
              <div
                key={i}
                className="bg-slate-800 rounded-2xl p-6 animate-pulse space-y-4 h-full"
              >
                <div className="h-5 bg-slate-700 rounded w-1/2" />
                <div className="h-4 bg-slate-700 rounded w-3/4" />
                <div className="h-4 bg-slate-700 rounded w-2/3" />
              </div>
            ))}
          </div>
        )}

        {/* STATE ERROR: Menampilkan pesan error dan tombol coba lagi */}
        {error && (
          <div className="bg-red-500/5 border border-red-500/20 rounded-2xl p-6 text-center flex-grow flex flex-col justify-center items-center gap-4">
            <AlertCircle className="w-8 h-8 text-red-500" />
            <p className="font-sans text-sm text-white/80 font-medium">{error}</p>
            <button
              onClick={fetchRates}
              className="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-slate-950 text-sm font-semibold rounded-lg hover:bg-sky-400 transition-colors"
            >
              <RefreshCw className="w-4 h-4" />
              Coba Lagi
            </button>
          </div>
        )}

        {/* STATE DATA READY: Render kartu informasi rincian biaya */}
        {(!loading || rates.length > 0) && !error && (
          <div className="flex-grow flex flex-col justify-between gap-8">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
              {rates.map((rate) => (
                <div
                  key={rate.type}
                  className="bg-slate-800/40 p-6 rounded-2xl border border-white/5 hover:border-sky-500/30 hover:bg-slate-800/60 transition-all duration-300 flex flex-col justify-between h-full"
                >
                  <div>
                    {/* Baris Judul Kartu Tarif */}
                    <div className="flex items-center justify-between mb-4 pb-4 border-b border-white/10">
                      <span className="font-display font-bold text-lg text-white tracking-wide uppercase">
                        {rate.name}
                      </span>
                      <div className="p-2 bg-sky-500/10 rounded-lg border border-sky-500/20">
                        {getIcon(rate.type)}
                      </div>
                    </div>

                    {/* Baris Rincian Biaya */}
                    <div className="space-y-4">
                      {/* Jam Pertama */}
                      <div>
                        <span className="block text-xs text-white/60 uppercase tracking-wider mb-1">Jam Pertama</span>
                        <span className="font-mono text-xl font-bold text-sky-400">
                          {formatRupiah(rate.first_hour_rate)}
                        </span>
                      </div>

                      {/* Jam Berikutnya */}
                      <div>
                        <span className="block text-xs text-white/60 uppercase tracking-wider mb-1">Jam Berikutnya</span>
                        <span className="font-mono text-xl font-bold text-slate-200">
                          {formatRupiah(rate.subsequent_hour_rate)} <span className="text-xs text-white/40 font-normal font-sans">/ jam</span>
                        </span>
                      </div>
                      
                      {/* Maksimal Harian & Denda Tiket Hilang */}
                      <div className="pt-4 mt-4 border-t border-white/10 space-y-3">
                        <div className="flex justify-between items-center">
                          <span className="text-xs text-white/60">Maksimal Harian</span>
                          <span className="font-mono text-sm font-semibold text-slate-200">
                            {rate.daily_max_rate !== null ? formatRupiah(rate.daily_max_rate) : "Tidak ada batas"}
                          </span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="flex items-center gap-1.5 text-xs text-white/60">
                            <Coins className="w-3.5 h-3.5 text-red-400/80" />
                            Denda Karcis Hilang
                          </span>
                          <span className="font-mono text-sm font-semibold text-red-400">
                            {formatRupiah(rate.fine_lost_ticket)}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
            
            {/* Disclaimer kebijakan manajemen di bagian bawah */}
            <div className="pt-6 border-t border-white/5 text-center mt-auto">
              <p className="text-xs text-slate-400 italic font-sans flex items-center justify-center gap-2">
                <AlertCircle className="w-4 h-4 text-sky-400/70" />
                * Tarif parkir dapat berubah sewaktu-waktu sesuai kebijakan manajemen.
              </p>
            </div>
          </div>
        )}
      </Card>
    </Section>
  );
}
