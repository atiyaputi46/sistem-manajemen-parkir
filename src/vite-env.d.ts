/// <reference types="vite/client" />

// Deklarasi modul untuk GSAP Premium Plugins agar TypeScript tidak menampilkan error merah jika tipe bawaannya tidak terdeteksi
declare module 'gsap/SplitText' {
  export const SplitText: any;
}

declare module 'gsap/CustomEase' {
  export const CustomEase: any;
}
