import React, { useState } from "react";
import { CheckCircle2, AlertCircle, Phone, User, Tag, CarFront, RefreshCw, Zap, CreditCard, ShieldCheck } from "lucide-react";
import { API_BASE } from "../config";
import Section from "./Section";
import Card from "./Card";

// ============================================================================
// INTERFACE STATE & ERRORS FORM
// ============================================================================
interface FormState {
  nama: string; // Nama lengkap pendaftar
  plat: string; // Nomor plat kendaraan pendaftar
  jenis: string; // Jenis kendaraan: motor, mobil, truk
  hp: string; // Nomor telepon HP/WhatsApp pendaftar
}

interface FormErrors {
  nama?: string;
  plat?: string;
  jenis?: string;
  hp?: string;
  general?: string; // Menyimpan error global dari server/koneksi
}

// ============================================================================
// KOMPONEN MEMBERFORM (FORMULIR REGISTRASI MEMBER)
// ============================================================================
// Menyediakan formulir isian pendaftaran akses prioritas member dengan validasi
// penuh baik di client-side maupun pencocokan error dari API Laravel backend.
export default function MemberForm() {
  // Nilai awal default untuk form
  const initialFormState: FormState = {
    nama: "",
    plat: "",
    jenis: "",
    hp: "",
  };

  // State untuk data isian formulir
  const [form, setForm] = useState<FormState>(initialFormState);
  // State untuk menyimpan error validasi tiap input
  const [errors, setErrors] = useState<FormErrors>({});
  // State indikator pengiriman data (submitting)
  const [isSubmitting, setIsSubmitting] = useState(false);
  // State indikator keberhasilan pengiriman pendaftaran
  const [isSuccess, setIsSuccess] = useState(false);

  // Menangani perubahan input form saat user mengetik
  const handleInputChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>
  ) => {
    const { name, value } = e.target;
    setForm((prev) => ({
      ...prev,
      [name]: value,
    }));

    // Menghapus pesan error pada field terkait jika user mulai mengetik ulang
    if (errors[name as keyof FormErrors]) {
      setErrors((prev) => ({
        ...prev,
        [name]: undefined,
      }));
    }
  };

  // Merapikan input plat nomor (menghilangkan spasi berlebih dan memaksa huruf kapital)
  const handlePlatBlurOrSubmit = () => {
    setForm((prev) => ({
      ...prev,
      plat: prev.plat.trim().toUpperCase(),
    }));
  };

  // Fungsi validasi input pada sisi client (sebelum dikirim ke API)
  const validateClientSide = (): boolean => {
    const newErrors: FormErrors = {};

    // Validasi Nama Lengkap wajib diisi
    if (!form.nama.trim()) {
      newErrors.nama = "Nama Lengkap wajib diisi.";
    }

    // Validasi Plat Kendaraan wajib diisi
    if (!form.plat.trim()) {
      newErrors.plat = "Nomor Plat Kendaraan wajib diisi.";
    }

    // Validasi Jenis Kendaraan wajib dipilih
    if (!form.jenis) {
      newErrors.jenis = "Pilih jenis kendaraan Anda.";
    }

    // Validasi Nomor HP: hanya angka, minimal 10 digit
    const hpDigits = form.hp.replace(/\D/g, "");
    if (!form.hp.trim()) {
      newErrors.hp = "Nomor HP wajib diisi.";
    } else if (hpDigits.length < 10) {
      newErrors.hp = "Nomor HP minimal harus 10 digit angka.";
    }

    setErrors(newErrors);
    // Return true jika tidak ada properti error yang disetel
    return Object.keys(newErrors).length === 0;
  };

  // Menangani pengiriman form (submit event)
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    // Rapikan plat nomor sebelum validasi & submit
    handlePlatBlurOrSubmit();

    // Batalkan submit jika validasi sisi klien gagal
    if (!validateClientSide()) return;

    setIsSubmitting(true);
    setErrors({});

    const cleanedPlat = form.plat.trim().toUpperCase();
    const cleanedHp = form.hp.replace(/\D/g, "");

    // Membuat payload bilingual (Indonesia & Inggris) untuk jaminan kompatibilitas dengan skema Laravel backend
    const payload = {
      // Properti Bahasa Indonesia
      nama: form.nama.trim(),
      plat_nomor: cleanedPlat,
      jenis_kendaraan: form.jenis,
      nomor_hp: cleanedHp,

      // Properti Bahasa Inggris (Laravel API standard)
      name: form.nama.trim(),
      full_name: form.nama.trim(),
      license_plate: cleanedPlat,
      plate_number: cleanedPlat,
      vehicle_plate: cleanedPlat,
      vehicle_type: form.jenis,
      phone: cleanedHp,
    };

    // Mengirimkan request POST ke API /api/members
    try {
      const response = await fetch(`${API_BASE}/api/members`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json().catch(() => ({}));

      // HTTP 201 Created: Berhasil
      if (response.status === 201) {
        setIsSuccess(true);
        setForm(initialFormState);
      }
      // HTTP 422 Unprocessable Content: Gagal validasi backend Laravel
      else if (response.status === 422) {
        const serverErrors = data.errors || {};
        const mappedErrors: FormErrors = {};

        // Memetakan error validasi Laravel ke field form masing-masing di UI
        if (serverErrors.nama || serverErrors.name || serverErrors.full_name) {
          mappedErrors.nama = (serverErrors.nama || serverErrors.name || serverErrors.full_name)[0];
        }
        if (serverErrors.plat_nomor || serverErrors.license_plate || serverErrors.plate_number || serverErrors.vehicle_plate) {
          mappedErrors.plat = (serverErrors.plat_nomor || serverErrors.license_plate || serverErrors.plate_number || serverErrors.vehicle_plate)[0];
        }
        if (serverErrors.jenis_kendaraan || serverErrors.vehicle_type) {
          mappedErrors.jenis = (serverErrors.jenis_kendaraan || serverErrors.vehicle_type)[0];
        }
        if (serverErrors.nomor_hp || serverErrors.phone) {
          mappedErrors.hp = (serverErrors.nomor_hp || serverErrors.phone)[0];
        }

        // Fallback jika tidak ada field yang terpetakan
        if (Object.keys(mappedErrors).length === 0) {
          mappedErrors.general = data.message || "Validasi server gagal.";
        }

        setErrors(mappedErrors);
      }
      // Status error HTTP lainnya
      else {
        setErrors({
          general: data.message || "Gagal mengirim. Coba beberapa saat lagi.",
        });
      }
    } catch (err) {
      console.error("Network or connection error:", err);
      setErrors({
        general: "Gagal mengirim. Hubungan server terputus. Coba beberapa saat lagi.",
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  // Reset form kembali ke kondisi awal (untuk pendaftaran baru)
  const handleReset = () => {
    setIsSuccess(false);
    setErrors({});
    setForm(initialFormState);
  };

  return (
    <Section id="daftar-member">
      <Card className="p-6 md:p-8 flex flex-col justify-between min-h-[600px]">
        {/* Garis aksen gradien di bagian bawah card */}
        <div className="absolute bottom-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-sky-500 to-transparent opacity-50" />

        {/* Bagian Header Form */}
        <div className="mb-8">
          <h2 className="font-display font-bold text-3xl md:text-4xl text-white">
            Akses Prioritas Member
          </h2>
          <p className="font-sans text-sm text-white/60 mt-2">
            Aktivasi gratis oleh admin di loket gerbang.
          </p>
        </div>

        {/* TAMPILAN JIKA PENDAFTARAN BERHASIL (SUCCESS STATE) */}
        {isSuccess ? (
          <div className="flex-grow flex flex-col justify-center items-center text-center p-8 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
            <CheckCircle2 className="w-16 h-16 text-emerald-500 mb-4 animate-bounce" />
            <h3 className="font-display font-bold text-xl text-white mb-2">
              Pendaftaran Berhasil!
            </h3>
            <p className="font-sans text-sm text-white/80 mb-8 max-w-sm leading-relaxed">
              Data Anda telah tersimpan. Admin kami akan segera menghubungi WhatsApp/HP Anda untuk proses aktivasi kartu member.
            </p>
            <button
              onClick={handleReset}
              className="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-sky-500 text-sky-400 text-sm font-semibold rounded-lg transition-all duration-300 hover:bg-sky-500 hover:text-slate-950 cursor-pointer"
            >
              Selesai
            </button>
          </div>
        ) : (
          /* TAMPILAN FORMULIR ISIAN (INPUT STATE) */
          <div className="flex-grow flex flex-col justify-between">

            {/* Menampilkan pesan error global dari server */}
            {errors.general && (
              <div className="mb-6 bg-red-500/10 border border-red-500/20 rounded-xl p-4 flex gap-3 text-sm text-red-400">
                <AlertCircle className="w-5 h-5 shrink-0" />
                <span>{errors.general}</span>
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">

              {/* Baris Grid 1: Nama Lengkap & Plat Kendaraan */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {/* Input Field: Nama Lengkap */}
                <div className="flex flex-col gap-2 group">
                  <label htmlFor="nama" className="text-xs font-bold uppercase tracking-wider text-white/60 flex items-center gap-2">
                    <User className="w-4 h-4 text-sky-400" />
                    Nama Lengkap
                  </label>
                  <input
                    type="text"
                    id="nama"
                    name="nama"
                    value={form.nama}
                    onChange={handleInputChange}
                    placeholder="Budi Santoso"
                    className={`bg-slate-800/40 border-b-2 ${errors.nama ? "border-red-500" : "border-white/10 group-focus-within:border-sky-400"
                      } focus:border-sky-400 outline-none py-3 px-4 text-sm text-white transition-all duration-300 placeholder-white/20 font-sans rounded-t-lg`}
                    disabled={isSubmitting}
                  />
                  {errors.nama && (
                    <p className="text-xs text-red-400 mt-1 flex items-center gap-1.5">
                      <AlertCircle className="w-3 h-3" />
                      {errors.nama}
                    </p>
                  )}
                </div>

                {/* Input Field: Plat Nomor */}
                <div className="flex flex-col gap-2 group">
                  <label htmlFor="plat" className="text-xs font-bold uppercase tracking-wider text-white/60 flex items-center gap-2">
                    <Tag className="w-4 h-4 text-sky-400" />
                    Plat Kendaraan
                  </label>
                  <input
                    type="text"
                    id="plat"
                    name="plat"
                    value={form.plat}
                    onChange={handleInputChange}
                    onBlur={handlePlatBlurOrSubmit}
                    placeholder="B 1234 CD"
                    className={`bg-slate-800/40 border-b-2 ${errors.plat ? "border-red-500" : "border-white/10 group-focus-within:border-sky-400"
                      } focus:border-sky-400 outline-none py-3 px-4 text-sm text-white uppercase transition-all duration-300 placeholder-white/20 font-sans rounded-t-lg`}
                    disabled={isSubmitting}
                  />
                  {errors.plat && (
                    <p className="text-xs text-red-400 mt-1 flex items-center gap-1.5">
                      <AlertCircle className="w-3 h-3" />
                      {errors.plat}
                    </p>
                  )}
                </div>
              </div>

              {/* Baris Grid 2: Jenis Kendaraan & Nomor HP */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {/* Select Field: Jenis Kendaraan */}
                <div className="flex flex-col gap-2 group">
                  <label htmlFor="jenis" className="text-xs font-bold uppercase tracking-wider text-white/60 flex items-center gap-2">
                    <CarFront className="w-4 h-4 text-sky-400" />
                    Jenis Kendaraan
                  </label>
                  <div className="relative">
                    <select
                      id="jenis"
                      name="jenis"
                      value={form.jenis}
                      onChange={handleInputChange}
                      className={`w-full bg-slate-800/40 border-b-2 ${errors.jenis ? "border-red-500" : "border-white/10 focus:border-sky-400"
                        } outline-none py-3 px-4 text-sm text-white transition-all duration-300 appearance-none font-sans cursor-pointer rounded-t-lg`}
                      disabled={isSubmitting}
                    >
                      <option value="" className="bg-[#0F172A] text-slate-500">Pilih jenis...</option>
                      <option value="motor" className="bg-[#0F172A] text-white">Motor</option>
                      <option value="mobil" className="bg-[#0F172A] text-white">Mobil</option>
                      <option value="truk" className="bg-[#0F172A] text-white">Truk</option>
                    </select>
                    {/* Segitiga dropdown indikator kustom */}
                    <div className="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-white/40 text-xs">
                      ▼
                    </div>
                  </div>
                  {errors.jenis && (
                    <p className="text-xs text-red-400 mt-1 flex items-center gap-1.5">
                      <AlertCircle className="w-3 h-3" />
                      {errors.jenis}
                    </p>
                  )}
                </div>

                {/* Input Field: Nomor WhatsApp/HP */}
                <div className="flex flex-col gap-2 group">
                  <label htmlFor="hp" className="text-xs font-bold uppercase tracking-wider text-white/60 flex items-center gap-2">
                    <Phone className="w-4 h-4 text-sky-400" />
                    WhatsApp / HP
                  </label>
                  <input
                    type="tel"
                    id="hp"
                    name="hp"
                    value={form.hp}
                    onChange={handleInputChange}
                    placeholder="081234567890"
                    className={`bg-slate-800/40 border-b-2 ${errors.hp ? "border-red-500" : "border-white/10 group-focus-within:border-sky-400"
                      } focus:border-sky-400 outline-none py-3 px-4 text-sm text-white transition-all duration-300 placeholder-white/20 font-sans rounded-t-lg`}
                    disabled={isSubmitting}
                  />
                  {errors.hp && (
                    <p className="text-xs text-red-400 mt-1 flex items-center gap-1.5">
                      <AlertCircle className="w-3 h-3" />
                      {errors.hp}
                    </p>
                  )}
                </div>
              </div>

              {/* Tombol Kirim Pendaftaran */}
              <div className="pt-6">
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full flex justify-center items-center py-4 px-6 bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-sm rounded-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-sky-500/20 cursor-pointer"
                >
                  {isSubmitting ? (
                    <span className="flex items-center gap-3">
                      <RefreshCw className="w-5 h-5 animate-spin" />
                      Mengirim...
                    </span>
                  ) : (
                    "Kirim Pendaftaran"
                  )}
                </button>
              </div>

            </form>

            {/* Bagian Keuntungan Member (Bento Layout pendukung) */}
            <div className="pt-8 mt-8 border-t border-white/5">
              <p className="text-xs font-bold text-white/40 uppercase tracking-wider mb-4 text-center">Keuntungan Menjadi Member</p>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {/* 1. Akses Instan */}
                <div className="flex items-start gap-3 p-4 rounded-xl bg-slate-800/40 border border-white/5 hover:border-sky-500/30 hover:bg-slate-800/60 transition-colors group">
                  <div className="p-2 bg-sky-500/10 rounded-lg shrink-0 group-hover:scale-110 transition-transform">
                    <Zap className="w-4 h-4 text-sky-400" />
                  </div>
                  <div>
                    <h4 className="text-sm font-bold text-white mb-1">Akses Instan</h4>
                    <p className="text-xs text-white/50 leading-relaxed">Keluar masuk gerbang tanpa perlu ambil karcis fisik.</p>
                  </div>
                </div>

                {/* 2. Pembayaran Praktis */}
                <div className="flex items-start gap-3 p-4 rounded-xl bg-slate-800/40 border border-white/5 hover:border-sky-500/30 hover:bg-slate-800/60 transition-colors group">
                  <div className="p-2 bg-sky-500/10 rounded-lg shrink-0 group-hover:scale-110 transition-transform">
                    <CreditCard className="w-4 h-4 text-sky-400" />
                  </div>
                  <div>
                    <h4 className="text-sm font-bold text-white mb-1">Bayar Praktis</h4>
                    <p className="text-xs text-white/50 leading-relaxed">Bebas antre bayar tunai dengan saldo e-wallet terintegrasi.</p>
                  </div>
                </div>

                {/* 3. Prioritas Keamanan */}
                <div className="flex items-start gap-3 p-4 rounded-xl bg-slate-800/40 border border-white/5 hover:border-sky-500/30 hover:bg-slate-800/60 transition-colors group">
                  <div className="p-2 bg-sky-500/10 rounded-lg shrink-0 group-hover:scale-110 transition-transform">
                    <ShieldCheck className="w-4 h-4 text-sky-400" />
                  </div>
                  <div>
                    <h4 className="text-sm font-bold text-white mb-1">Prioritas Keamanan</h4>
                    <p className="text-xs text-white/50 leading-relaxed">Slot prioritas dan pantauan CCTV khusus untuk kendaraan member.</p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        )}
      </Card>
    </Section>
  );
}
