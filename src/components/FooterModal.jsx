import React, { useEffect } from 'react';
import { X } from 'lucide-react';

/**
 * FooterModal Component
 * Renders a premium, accessible modal popup for legal information and help guides.
 */
export default function FooterModal({ isOpen, onClose, title, icon, items }) {
  // Dynamically inject Tabler Icons CDN if not already loaded in the DOM
  useEffect(() => {
    const linkId = 'tabler-icons-cdn';
    if (!document.getElementById(linkId)) {
      const link = document.createElement('link');
      link.id = linkId;
      link.rel = 'stylesheet';
      link.href = 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css';
      document.head.appendChild(link);
    }
  }, []);

  // Handle Escape key to close the modal
  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === 'Escape') {
        onClose();
      }
    };
    if (isOpen) {
      window.addEventListener('keydown', handleKeyDown);
    }
    return () => {
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [isOpen, onClose]);

  // Lock and unlock body scrolling when modal open status changes
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isOpen]);

  if (!isOpen) return null;

  return (
    <div
      className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-xs animate-fade-in"
      onClick={onClose}
      role="dialog"
      aria-modal="true"
    >
      <div
        className="max-w-[460px] w-full bg-[#0F172A] border border-white/10 rounded-[12px] overflow-hidden shadow-2xl flex flex-col transform transition-all duration-300 scale-100"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header Modal */}
        <div className="flex items-center justify-between px-5 py-4 border-b border-white/5 bg-slate-900/20">
          <div className="flex items-center gap-3 text-white">
            {icon && (
              <div className="text-sky-400 text-lg flex items-center justify-center">
                <i className={icon}></i>
              </div>
            )}
            <h3 className="font-display font-bold text-sm tracking-wider uppercase">
              {title}
            </h3>
          </div>
          <button
            onClick={onClose}
            aria-label="Tutup"
            className="text-white/40 hover:text-white transition-colors duration-200 cursor-pointer p-1 rounded-md hover:bg-white/5"
          >
            <X className="w-4.5 h-4.5" />
          </button>
        </div>

        {/* Body Modal */}
        <div className="p-5 flex flex-col gap-4 max-h-[60vh] overflow-y-auto">
          {items && items.map((item, idx) => {
            const bgOpacity = item.bgOpacity || '15%';
            // Use color-mix to handle dynamic solid color + opacity cleanly in CSS
            const bgStyle = {
              backgroundColor: `color-mix(in srgb, ${item.bgColor} ${bgOpacity}, transparent)`
            };
            const iconColor = item.textColor || item.bgColor;

            return (
              <div key={idx} className="flex items-start gap-4 p-1 rounded-lg hover:bg-white/[0.01] transition-all duration-250">
                {/* 34x34px Small Icon Box */}
                <div
                  className="w-[34px] h-[34px] rounded-[8px] flex items-center justify-center shrink-0 border border-white/[0.03]"
                  style={bgStyle}
                >
                  <i
                    className={`${item.icon} text-base`}
                    style={{ color: iconColor }}
                  ></i>
                </div>
                {/* Content Text */}
                <div className="flex flex-col gap-1">
                  <h4 className="font-sans font-medium text-sm text-white/90">
                    {item.title}
                  </h4>
                  <p className="font-sans text-xs text-white/50 leading-relaxed">
                    {item.desc}
                  </p>
                </div>
              </div>
            );
          })}
        </div>

        {/* Footer Modal */}
        <div className="p-4 border-t border-white/5 bg-slate-900/10">
          <button
            onClick={onClose}
            className="w-full py-3 px-4 bg-sky-500 hover:bg-sky-400 active:scale-[0.98] text-slate-950 font-bold text-sm rounded-lg transition-all duration-300 hover:shadow-lg hover:shadow-sky-500/20 cursor-pointer"
          >
            Mengerti
          </button>
        </div>
      </div>
    </div>
  );
}
