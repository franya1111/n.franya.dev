'use client';

import { useEffect, useRef, useState } from 'react';
import { faqs } from '@/lib/site-data';

export function Faq() {
  const [visible, setVisible] = useState(false);
  const [open, setOpen] = useState<number | null>(0);
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

  const toggle = (i: number) => setOpen(open === i ? null : i);

  return (
    <section id="faq" ref={ref} className="bg-secondary/30 py-20 md:py-28 lg:py-36">
      <div className="mx-auto max-w-[760px] px-6">
        <div className="mb-12 text-center md:mb-16">
          <span className="section-label">FAQ</span>
          <h2 className="section-title">Питання та відповіді</h2>
          <p className="mx-auto mt-4 max-w-[500px] text-[15px] text-muted-foreground">
            Зібрав найчастіші питання — якщо вашого тут немає, напишіть мені особисто.
          </p>
        </div>

        <div className="space-y-3">
          {faqs.map((f, i) => (
            <div
              key={i}
              className={`overflow-hidden rounded-xl border border-border/60 bg-card/60 transition-all reveal ${
                visible ? 'visible' : ''
              } ${open === i ? 'border-[var(--gold,#c9a96e)]/40' : ''}`}
              style={{ transitionDelay: `${i * 40}ms` }}
            >
              <button
                onClick={() => toggle(i)}
                className="flex w-full items-center justify-between gap-4 px-5 py-5 text-left md:px-6 md:py-6"
                aria-expanded={open === i}
              >
                <span className="font-serif text-base text-foreground md:text-lg">
                  {f.q}
                </span>
                <span
                  className={`flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-border/60 text-[var(--gold,#c9a96e)] transition-all ${
                    open === i ? 'rotate-180 bg-[var(--gold,#c9a96e)] text-[var(--background,#0a0a0a)]' : ''
                  }`}
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-3 w-3">
                    <polyline points="6 9 12 15 18 9" />
                  </svg>
                </span>
              </button>
              <div
                className="grid transition-all duration-500 ease-in-out"
                style={{
                  gridTemplateRows: open === i ? '1fr' : '0fr',
                }}
              >
                <div className="overflow-hidden">
                  <p className="px-5 pb-5 text-[14px] leading-relaxed text-muted-foreground md:px-6 md:pb-6 md:text-[15px]">
                    {f.a}
                  </p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
