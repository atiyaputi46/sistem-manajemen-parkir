import {StrictMode} from 'react';
import {createRoot} from 'react-dom/client';
import App from './App.tsx';
import './index.css';

// ============================================================================
// ENTRYPOINT APLIKASI REACT
// ============================================================================

// Mencari elemen kontainer dengan id 'root' pada dokumen HTML (index.html).
// Elemen ditandai dengan operator non-null assertion (!) karena diasumsikan selalu ada.
//
// Metode createRoot digunakan untuk menginisialisasi root React 19.
// Di dalamnya, komponen <App /> dibungkus dengan <StrictMode> untuk mengaktifkan
// pemeriksaan dan peringatan tambahan terkait praktek-praktek penulisan React yang baik.
createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <App />
  </StrictMode>,
);
