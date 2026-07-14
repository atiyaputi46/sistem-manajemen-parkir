// ============================================================================
// KONFIGURASI BASIS URL API
// ============================================================================

// Mengambil nilai basis URL untuk API dari environment variable (VITE_API_BASE).
// Jika tidak disetel, gunakan string kosong ('') agar request /api/... dikirim 
// relatif ke origin (domain & port) yang sama dengan frontend.
//
// Pada mode development, Vite Dev Server (vite.config.ts) dikonfigurasi untuk 
// mem-proxy request yang mengarah ke '/api' menuju backend Laravel di localhost:8000.
// Pada mode production, set variabel VITE_API_BASE ke alamat URL backend yang asli.
export const API_BASE = import.meta.env.VITE_API_BASE ?? '';

