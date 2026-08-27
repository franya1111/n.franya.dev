'use client';

import { useEffect, useState } from 'react';

export function Loader() {
  const [hidden, setHidden] = useState(false);
  const [gone, setGone] = useState(false);

  useEffect(() => {
    const t1 = setTimeout(() => setHidden(true), 1400);
    const t2 = setTimeout(() => setGone(true), 2200);
    return () => {
      clearTimeout(t1);
      clearTimeout(t2);
    };
  }, []);

  if (gone) return null;

  return (
    <div
      className={`fixed inset-0 z-[9999] flex items-center justify-center bg-[#0a0a0a] transition-opacity duration-700 ${
        hidden ? 'opacity-0' : 'opacity-100'
      }`}
      style={{ visibility: hidden ? 'hidden' : 'visible' }}
    >
      <span
        className="font-serif text-[clamp(20px,5vw,40px)] tracking-[0.3em] text-[var(--gold,#c9a96e)]"
        style={{
          animation: 'loader-shimmer 1.4s ease-in-out infinite',
        }}
      >
        krasnobaeva
      </span>
      <style>{`
        @keyframes loader-shimmer {
          0%, 100% { opacity: 0.4; letter-spacing: 0.25em; }
          50% { opacity: 1; letter-spacing: 0.35em; }
        }
      `}</style>
    </div>
  );
}
