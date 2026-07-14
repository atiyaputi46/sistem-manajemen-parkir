import React from 'react';

// ============================================================================
// INTERFACE SECTION PROPS
// ============================================================================
interface SectionProps {
  // ID unik elemen untuk keperluan navigasi smooth scroll
  id?: string;
  // Kelas CSS tambahan opsional untuk penyesuaian gaya kontainer
  className?: string;
  // Elemen anak yang akan dirender di dalam komponen Section
  children: React.ReactNode;
}

// ============================================================================
// KOMPONEN SECTION
// ============================================================================
// Komponen tata letak seksi halaman lebar penuh (w-full) dengan lebar maksimal 
// konten terpusat (max-w-7xl mx-auto) dan padding responsif.
export default function Section({ id, className = '', children }: SectionProps) {
  return (
    <section id={id} className={`w-full py-8 md:py-12 flex justify-center ${className}`}>
      <div className="w-full max-w-7xl mx-auto px-6 md:px-8">
        {children}
      </div>
    </section>
  );
}
