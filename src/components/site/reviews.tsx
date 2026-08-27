'use client';

import { useEffect, useRef, useState } from 'react';
import { reviews } from '@/lib/site-data';

export function Reviews() {
  const [visible, setVisible] = useState(false);
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    const obs = new IntersectionObserver(
      ([e]) => {
        if (e.isIntersecting) {
          setVisible(true);
          obs.disconnect();
        }
      },
      { threshold: 0.1 }
    );
    obs.observe(el);
    return () => obs.disconnect();
  }, []);

  // Duplicate reviews to create infinite scroll illusion
  const doubled = [...reviews, ...reviews];

  return (
    <section id="reviews" ref={ref} className="overflow-hidden py-20 md:py-28 lg:py-36">
      <div className="mx-auto max-w-[1200px] px-6">
        <div className="mb-12 flex flex-col items-center text-center md:mb-16">
          <span className="section-label">Відгуки</span>
          <h2 className="section-title">Що кажуть клієнти</h2>
          <p className="mx-auto mt-4 max-w-[560px] text-[15px] text-muted-foreground">
            Понад 200 зйомок за 8 років — кожна зі своєю історією. Ось лише деякі відгуки.
          </p>
          <a
            href="https://t.me/krasnobaevaph"
            target="_blank"
            rel="noopener noreferrer"
            className="btn-ghost mt-8"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-4 w-4">
              <line x1="22" y1="2" x2="11" y2="13" />
              <polygon points="22 2 15 22 11 13 2 9 22 2" />
            </svg>
            Залишити відгук
          </a>
        </div>
      </div>

      {/* Auto-scrolling marquee */}
      <div className={`relative reveal ${visible ? 'visible' : ''}`}>
        {/* Edge fades */}
        <div className="pointer-events-none absolute left-0 top-0 z-10 h-full w-32 bg-gradient-to-r from-background to-transparent md:w-64" />
        <div className="pointer-events-none absolute right-0 top-0 z-10 h-full w-32 bg-gradient-to-l from-background to-transparent md:w-64" />

        <div className="marquee-track gap-5 px-6 md:gap-6">
          {doubled.map((r, idx) => (
            <article
              key={idx}
              className="flex w-[280px] shrink-0 flex-col justify-between rounded-2xl border border-border/60 bg-card/70 p-6 backdrop-blur-sm transition-all hover:border-[var(--gold,#c9a96e)]/60 hover:bg-card md:w-[340px] md:p-8"
            >
              <div>
                <div className="mb-3 flex gap-1">
                  {[0, 1, 2, 3, 4].map((s) => (
                    <svg key={s} viewBox="0 0 24 24" fill="currentColor" className="h-3 w-3 text-[var(--gold,#c9a96e)]">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                  ))}
                </div>
                <p className="text-[14px] leading-relaxed text-foreground/90 md:text-[15px]">
                  &ldquo;{r.text}&rdquo;
                </p>
              </div>
              <div className="mt-6 flex items-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[var(--gold,#c9a96e)] to-[var(--gold-dark,#a8873f)] text-sm font-bold text-[var(--background,#0a0a0a)]">
                  {r.name.charAt(0)}
                </div>
                <div>
                  <p className="font-serif text-base text-foreground">{r.name}</p>
                  <p className="text-[10px] uppercase tracking-[0.2em] text-muted-foreground">Клієнт</p>
                </div>
              </div>
            </article>
          ))}
        </div>
      </div>

      <div className="mx-auto mt-12 max-w-[1200px] px-6 text-center">
        <p className="text-xs uppercase tracking-[0.3em] text-muted-foreground">
          ↑ Наведіть курсор, щоб зупинити прокрутку
        </p>
      </div>
    </section>
  );
}
