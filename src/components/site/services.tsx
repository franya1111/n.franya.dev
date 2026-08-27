'use client';

import { useEffect, useRef, useState } from 'react';
import { categories } from '@/lib/site-data';

type Props = {
  onSelectCategory: (id: string) => void;
};

export function Services({ onSelectCategory }: Props) {
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

  return (
    <section id="services-overview" ref={ref} className="bg-secondary/30 py-20 md:py-28 lg:py-36">
      <div className="mx-auto max-w-[1200px] px-6">
        <div className="mb-12 text-center md:mb-16">
          <span className="section-label">Що я пропоную</span>
          <h2 className="section-title">Мої послуги</h2>
          <p className="mx-auto mt-4 max-w-[600px] text-[15px] text-muted-foreground">
            Оберіть категорію зйомки — кожна веде на окрему сторінку з описом, прикладами та цінами.
          </p>
        </div>

        {/* 6 square cards */}
        <div className="grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 md:gap-6">
          {categories.map((cat, i) => (
            <button
              key={cat.id}
              onClick={() => onSelectCategory(cat.id)}
              className={`group relative block aspect-square overflow-hidden rounded-xl border border-border/60 bg-card reveal-scale ${
                visible ? 'visible' : ''
              }`}
              style={{ transitionDelay: `${i * 80}ms` }}
            >
              {/* Image */}
              { }
              <img
                src={cat.heroImage}
                alt={cat.title}
                className="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
              />
              {/* Overlay gradient */}
              <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-black/10 transition-opacity duration-500 group-hover:from-black/95 group-hover:via-black/50" />
              {/* Content */}
              <div className="absolute inset-0 flex flex-col items-center justify-end p-4 text-center md:p-6">
                <h3 className="font-serif text-lg tracking-wide text-white md:text-2xl">
                  {cat.title}
                </h3>
                <div className="mt-2 h-px w-0 bg-[var(--gold,#c9a96e)] transition-all duration-500 group-hover:w-12" />
                <span className="mt-3 text-[9px] font-semibold uppercase tracking-[0.25em] text-[var(--gold-light,#dfc397)] opacity-0 transition-all duration-500 group-hover:opacity-100 md:text-[10px]">
                  Детальніше →
                </span>
              </div>
              {/* Corner accent */}
              <div className="absolute right-3 top-3 h-8 w-8 opacity-0 transition-all duration-500 group-hover:opacity-100">
                <div className="absolute right-0 top-0 h-full w-px bg-[var(--gold,#c9a96e)]" />
                <div className="absolute right-0 top-0 h-px w-full bg-[var(--gold,#c9a96e)]" />
              </div>
            </button>
          ))}
        </div>
      </div>
    </section>
  );
}
