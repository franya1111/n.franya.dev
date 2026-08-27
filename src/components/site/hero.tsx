'use client';

import { useEffect, useState } from 'react';

export function Hero({ onExplore }: { onExplore: () => void }) {
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    const t = setTimeout(() => setLoaded(true), 100);
    return () => clearTimeout(t);
  }, []);

  return (
    <section id="hero" className="relative flex h-screen min-h-[600px] items-center justify-center overflow-hidden">
      {/* Background image */}
      <div className="absolute inset-0 z-0">
        { }
        <img
          src="/images/hero.jpg"
          alt="Весільна фотографія"
          className={`h-full w-full object-cover transition-opacity duration-[1500ms] ${loaded ? 'opacity-100' : 'opacity-0'}`}
        />
        <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/40 to-black/80" />
      </div>

      {/* Decorative circles */}
      <div className="pointer-events-none absolute inset-0 z-10">
        <div
          className="absolute left-[10%] top-[20%] h-32 w-32 rounded-full border border-[var(--gold,#c9a96e)]/30"
          style={{ animation: 'rotate-slow 24s linear infinite' }}
        />
        <div
          className="absolute right-[12%] bottom-[25%] h-48 w-48 rounded-full border border-[var(--gold,#c9a96e)]/20"
          style={{ animation: 'rotate-slow 40s linear infinite reverse' }}
        />
      </div>

      {/* Content */}
      <div className="relative z-20 flex flex-col items-center px-6 text-center">
        <h1
          className={`font-serif text-[clamp(48px,12vw,140px)] tracking-[0.18em] text-white transition-all duration-1000 ${
            loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          }`}
        >
          krasnobaeva
        </h1>
        <p
          className={`mt-4 text-[clamp(12px,2.4vw,18px)] uppercase tracking-[0.4em] text-[var(--gold-light,#dfc397)] transition-all delay-200 duration-1000 ${
            loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'
          }`}
        >
          photo &amp; video
        </p>
        <div
          className={`mt-8 h-px bg-gradient-to-r from-transparent via-[var(--gold,#c9a96e)] to-transparent transition-all delay-500 duration-1000 ${
            loaded ? 'w-40 opacity-100' : 'w-0 opacity-0'
          }`}
        />
      </div>

      {/* Scroll indicator */}
      <button
        onClick={onExplore}
        className="absolute bottom-8 left-1/2 z-20 -translate-x-1/2 text-white/70 transition-colors hover:text-[var(--gold,#c9a96e)]"
        aria-label="Scroll down"
      >
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth={2}
          className="h-7 w-7"
          style={{ animation: 'bounce-down 2s ease-in-out infinite' }}
        >
          <polyline points="6 9 12 15 18 9" />
        </svg>
      </button>

      <style>{`
        @keyframes bounce-down {
          0%, 100% { transform: translateX(-50%) translateY(0); }
          50% { transform: translateX(-50%) translateY(12px); }
        }
      `}</style>
    </section>
  );
}
