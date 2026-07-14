import { type ReactNode } from 'react';

// ============================================================================
// INTERFACE CARD PROPS
// ============================================================================
interface CardProps {
  // Kelas CSS tambahan opsional untuk penyesuaian gaya eksternal
  className?: string;
  // Elemen anak yang akan dirender di dalam komponen Card
  children: ReactNode;
}

// ============================================================================
// KOMPONEN CARD
// ============================================================================
// Komponen pembungkus bergaya Bento Grid dengan latar belakang gelap (#0F172A),
// sudut tumpul (rounded-2xl), bayangan lembut, serta efek transisi interaktif.
export default function Card({ className = '', children }: CardProps) {
  return (
    <div className={`bg-[#0F172A] rounded-2xl border border-white/5 shadow-xl relative overflow-hidden group ${className}`}>
      {children}
    </div>
  );
}
