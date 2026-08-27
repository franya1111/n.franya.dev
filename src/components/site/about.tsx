'use client';

import { useEffect, useRef, useState } from 'react';

export function About() {
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
      { threshold: 0.15 }
    );
    obs.observe(el);
    return () => obs.disconnect();
  }, []);

  return (
    <section id="about" ref={ref} className="py-20 md:py-28 lg:py-36">
      <div className="mx-auto max-w-[1200px] px-6">
        <div className="grid gap-12 md:grid-cols-2 md:gap-16 lg:gap-24">
          {/* Image */}
          <div className={`relative reveal-left ${visible ? 'visible' : ''}`}>
            <div className="absolute -left-4 -top-4 h-full w-full rounded-2xl border border-[var(--gold,#c9a96e)]/30" />
            <div className="relative overflow-hidden rounded-2xl">
              { }
              <img
                src="/images/about.jpg"
                alt="Фотограф Тетяна Краснобаєва"
                className="aspect-[4/5] w-full object-cover transition-transform duration-700 hover:scale-105"
              />
            </div>
          </div>

          {/* Text */}
          <div className={`flex flex-col justify-center reveal-right ${visible ? 'visible' : ''}`}>
            <span className="section-label">Про мене</span>
            <h2 className="section-title mb-6">Привіт!</h2>
            <div className="space-y-4 text-base leading-relaxed text-muted-foreground md:text-[15px]">
              <p>
                Я — Тетяна, фотограф, який допомагає не просто отримати гарні фото, а зберегти справжні емоції та атмосферу моменту.
              </p>
              <p>
                Спеціалізуюсь на індивідуальних, сімейних і весільних зйомках. У своїй роботі поєдную естетику, комфорт та уважність до деталей, щоб у кадрі ви залишились собою — живими, природними та впевненими.
              </p>
              <p>
                Для мене важливо створити не лише красиву картинку, а й сам процес, у якому легко розслабитись і отримувати задоволення від зйомки. Я допомагаю з позуванням, контролюю всі деталі та створюю атмосферу, в якій не потрібно хвилюватися про те, «як стати» чи «що робити».
              </p>
              <p>
                Понад <strong className="font-semibold text-foreground">8 років досвіду</strong> навчили мене головному: найкращі кадри народжуються тоді, коли людина почувається комфортно.
              </p>
              <p>Моя мета — фотографії, до яких хочеться повертатися через роки.</p>
            </div>
            <a
              href="https://www.instagram.com/krasnobaeva.ph/"
              target="_blank"
              rel="noopener noreferrer"
              className="mt-8 inline-flex items-center gap-3 self-start rounded-lg border border-[var(--gold,#c9a96e)] px-6 py-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-[var(--gold,#c9a96e)] transition-all hover:bg-[var(--gold,#c9a96e)] hover:text-[var(--background,#0a0a0a)] hover:shadow-[0_8px_25px_rgba(201,169,110,0.25)]"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-4 w-4">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
              </svg>
              @krasnobaeva.ph
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}
