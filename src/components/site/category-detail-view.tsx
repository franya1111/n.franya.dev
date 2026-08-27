'use client';

import { useEffect, useRef, useState } from 'react';
import type { Category } from '@/lib/site-data';

type Props = {
  category: Category;
  onBack: () => void;
  onSelectCategory: (id: string) => void;
  onBookNow: () => void;
};

export function CategoryDetailView({ category, onBack, onBookNow }: Props) {
  const [heroVisible, setHeroVisible] = useState(false);
  const [openExtra, setOpenExtra] = useState<number | null>(0);
  const sectionRefs = useRef<Record<string, HTMLElement | null>>({});

  useEffect(() => {
    const t = setTimeout(() => setHeroVisible(true), 50);
    return () => clearTimeout(t);
  }, [category.id]);

  useEffect(() => {
    window.scrollTo({ top: 0, behavior: 'instant' as ScrollBehavior });
  }, [category.id]);

  // Setup reveal observers for pricing cards
  useEffect(() => {
    const els = Array.from(document.querySelectorAll<HTMLElement>('[data-reveal]'));
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add('visible');
            obs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.1 }
    );
    els.forEach((el) => obs.observe(el));
    return () => obs.disconnect();
  }, [category.id]);

  return (
    <div className="view-fade">
      {/* HERO */}
      <section className="relative flex min-h-[70vh] items-center justify-center overflow-hidden pt-[72px] md:pt-[88px]">
        <div className="absolute inset-0 z-0">
          { }
          <img
            src={category.heroImage}
            alt={category.title}
            className={`h-full w-full object-cover transition-all duration-[1500ms] ${
              heroVisible ? 'scale-100 opacity-100 blur-0' : 'scale-110 opacity-0 blur-md'
            }`}
          />
          <div className="absolute inset-0 bg-gradient-to-b from-black/80 via-black/40 to-black/80" />
        </div>
        <div className="relative z-10 px-6 text-center">
          <span
            className={`text-[11px] uppercase tracking-[0.4em] text-[var(--gold-light,#dfc397)] transition-all duration-700 ${
              heroVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'
            }`}
          >
            Зйомка
          </span>
          <h1
            className={`mt-4 font-serif text-[clamp(40px,9vw,90px)] leading-none tracking-[0.05em] text-white transition-all delay-150 duration-700 ${
              heroVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'
            }`}
          >
            {category.title}
          </h1>
          <p
            className={`mx-auto mt-6 max-w-[560px] text-[15px] leading-relaxed text-white/80 transition-all delay-300 duration-700 ${
              heroVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'
            }`}
          >
            {category.description}
          </p>
          <button
            onClick={() => sectionRefs.current.packages?.scrollIntoView({ behavior: 'smooth', block: 'start' })}
            className={`btn-primary mt-8 transition-all delay-500 duration-700 ${
              heroVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'
            }`}
          >
            Переглянути пакети
          </button>
        </div>
      </section>

      {/* ABOUT THIS CATEGORY */}
      <section className="py-20 md:py-28">
        <div className="mx-auto max-w-[900px] px-6">
          <div className="text-center">
            <span className="section-label">Про зйомку</span>
            <h2 className="section-title">Як це проходить</h2>
          </div>
          <div className="mt-10 space-y-5 text-[15px] leading-relaxed text-muted-foreground md:text-base">
            {category.longDescription.map((p, i) => (
              <p key={i}>{p}</p>
            ))}
          </div>

          {/* Gallery preview */}
          <div className="mt-14 grid grid-cols-3 gap-3 md:gap-5">
            {category.gallery.map((src, i) => (
              <div
                key={i}
                className={`group relative aspect-[3/4] overflow-hidden rounded-xl border border-border/60 ${
                  i === 1 ? 'translate-y-0 md:translate-y-8' : ''
                }`}
              >
                { }
                <img
                  src={src}
                  alt={`${category.title} ${i + 1}`}
                  className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100" />
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* PRICING PACKAGES */}
      <section
        ref={(el) => { sectionRefs.current.packages = el; }}
        id="packages"
        className="bg-secondary/30 py-20 md:py-28 lg:py-32"
      >
        <div className="mx-auto max-w-[1200px] px-6">
          <div className="mb-12 text-center md:mb-16">
            <span className="section-label">Пакети</span>
            <h2 className="section-title">{category.title}</h2>
            <p className="mx-auto mt-4 max-w-[500px] text-[15px] text-muted-foreground">
              Оберіть свій пакет нижче — кожен можна адаптувати під ваші побажання.
            </p>
          </div>

          <div className="grid gap-6 md:grid-cols-3 md:gap-5">
            {category.packages.map((pkg, i) => (
              <div
                key={i}
                data-reveal
                className={`reveal relative flex flex-col overflow-hidden rounded-2xl border bg-card transition-all hover:shadow-2xl ${
                  pkg.popular
                    ? 'border-[var(--gold,#c9a96e)]/60 shadow-[0_0_60px_rgba(201,169,110,0.1)]'
                    : 'border-border/60'
                }`}
                style={{ transitionDelay: `${i * 100}ms` }}
              >
                {pkg.popular && <span className="popular-badge">Популярний</span>}

                {/* Package image */}
                <div className="relative aspect-[4/3] overflow-hidden">
                  { }
                  <img
                    src={pkg.image}
                    alt={pkg.name}
                    className="h-full w-full object-cover transition-transform duration-700 hover:scale-110"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                  <div className="absolute bottom-4 left-4 right-4 flex items-end justify-between">
                    <h3 className="font-serif text-2xl text-white md:text-3xl">{pkg.name}</h3>
                    <span className="rounded-full bg-[var(--gold,#c9a96e)]/95 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--background,#0a0a0a)]">
                      {pkg.duration}
                    </span>
                  </div>
                </div>

                {/* Body */}
                <div className="flex flex-1 flex-col p-6 md:p-7">
                  <div className="mb-5 flex items-baseline gap-2">
                    <span className="font-serif text-4xl text-[var(--gold,#c9a96e)] md:text-5xl">{pkg.price}</span>
                  </div>

                  <ul className="mb-7 space-y-3 text-[14px] text-muted-foreground">
                    {pkg.features.map((f, j) => (
                      <li key={j} className="flex items-start gap-3">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="mt-0.5 h-4 w-4 shrink-0 text-[var(--gold,#c9a96e)]">
                          <polyline points="20 6 9 17 4 12" />
                        </svg>
                        <span>{f}</span>
                      </li>
                    ))}
                  </ul>

                  <button onClick={onBookNow} className="btn-primary mt-auto w-full">
                    {pkg.cta}
                  </button>
                </div>
              </div>
            ))}
          </div>

          {/* GIFT */}
          <div
            data-reveal
            className="reveal mt-12 overflow-hidden rounded-2xl border border-[var(--gold,#c9a96e)]/30 bg-gradient-to-br from-card via-secondary/40 to-card p-7 md:mt-16 md:p-10"
          >
            <div className="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
              <div className="flex items-center gap-4">
                <div className="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-[var(--gold,#c9a96e)] to-[var(--gold-dark,#a8873f)]">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-7 w-7 text-[var(--background,#0a0a0a)]">
                    <polyline points="20 12 20 22 4 22 4 12" />
                    <rect x="2" y="7" width="20" height="5" />
                    <line x1="12" y1="22" x2="12" y2="7" />
                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z" />
                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z" />
                  </svg>
                </div>
                <div>
                  <h3 className="font-serif text-2xl text-foreground md:text-3xl">{category.gift.title}</h3>
                  <p className="mt-1 text-sm text-muted-foreground">{category.gift.note}</p>
                </div>
              </div>
              <div className="flex flex-wrap gap-2">
                {category.gift.items.map((g, i) => (
                  <span key={i} className="rounded-full border border-[var(--gold,#c9a96e)]/40 bg-card/60 px-4 py-2 text-[12px] font-semibold tracking-wide text-[var(--gold-light,#dfc397)]">
                    {g}
                  </span>
                ))}
              </div>
            </div>
          </div>

          {/* EXTRAS */}
          <div data-reveal className="reveal mt-12 md:mt-16">
            <h3 className="mb-6 text-center font-serif text-2xl text-foreground md:text-3xl">Додаткові послуги</h3>
            <div className="space-y-3">
              {category.extras.map((ex, i) => (
                <div
                  key={i}
                  className={`overflow-hidden rounded-xl border bg-card/60 transition-all ${
                    openExtra === i ? 'border-[var(--gold,#c9a96e)]/40' : 'border-border/60'
                  }`}
                >
                  <button
                    onClick={() => setOpenExtra(openExtra === i ? null : i)}
                    className="flex w-full items-center justify-between gap-4 px-5 py-4 text-left md:px-6 md:py-5"
                  >
                    <span className="font-serif text-base text-foreground md:text-lg">{ex.title}</span>
                    <span
                      className={`flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-border/60 text-[var(--gold,#c9a96e)] transition-all ${
                        openExtra === i ? 'rotate-180 bg-[var(--gold,#c9a96e)] text-[var(--background,#0a0a0a)]' : ''
                      }`}
                    >
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-3 w-3">
                        <polyline points="6 9 12 15 18 9" />
                      </svg>
                    </span>
                  </button>
                  <div
                    className="grid transition-all duration-500 ease-in-out"
                    style={{ gridTemplateRows: openExtra === i ? '1fr' : '0fr' }}
                  >
                    <div className="overflow-hidden">
                      <p className="px-5 pb-4 text-[14px] leading-relaxed text-muted-foreground md:px-6 md:pb-5">
                        {ex.description}
                      </p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 text-center md:py-24">
        <div className="mx-auto max-w-[640px] px-6">
          <h2 className="section-title">Готові забронювати?</h2>
          <p className="mx-auto mt-4 mb-8 max-w-[420px] text-[15px] text-muted-foreground">
            Заповніть коротку анкету — і я зв&apos;яжуся з вами для підтвердження деталей.
          </p>
          <div className="flex flex-wrap justify-center gap-3">
            <button onClick={onBookNow} className="btn-primary">
              Заповнити анкету
            </button>
            <button onClick={onBack} className="btn-ghost">
              ← Повернутись на головну
            </button>
          </div>
        </div>
      </section>
    </div>
  );
}
