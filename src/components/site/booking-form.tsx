'use client';

import { useEffect, useRef, useState } from 'react';
import { categories } from '@/lib/site-data';

export function BookingForm() {
  const [visible, setVisible] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [form, setForm] = useState({
    name: '',
    phone: '',
    category: '',
    date: '',
    message: '',
  });
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

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // Build a pre-filled Telegram message
    const text = `Нове бронювання%0A%0AІм'я: ${encodeURIComponent(form.name)}%0AТелефон: ${encodeURIComponent(form.phone)}%0AКатегорія: ${encodeURIComponent(form.category)}%0AДата: ${encodeURIComponent(form.date)}%0AПовідомлення: ${encodeURIComponent(form.message)}`;
    window.open(`https://t.me/krasnobaevaph?message=${text}`, '_blank');
    setSubmitted(true);
  };

  const update = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) =>
    setForm({ ...form, [k]: e.target.value });

  return (
    <section id="booking" ref={ref} className="py-20 md:py-28 lg:py-36">
      <div className="mx-auto max-w-[920px] px-6">
        <div
          className={`relative overflow-hidden rounded-3xl border border-[var(--gold,#c9a96e)]/20 bg-gradient-to-br from-card via-secondary/40 to-card p-8 reveal-scale md:p-12 lg:p-16 ${
            visible ? 'visible' : ''
          }`}
        >
          {/* Decorative element */}
          <div className="pointer-events-none absolute -right-20 -top-20 h-60 w-60 rounded-full bg-[var(--gold,#c9a96e)]/5 blur-3xl" />
          <div className="pointer-events-none absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-[var(--gold,#c9a96e)]/5 blur-3xl" />

          <div className="relative grid gap-10 md:grid-cols-2 md:gap-12">
            {/* Left side */}
            <div>
              <span className="section-label">Анкета бронювання</span>
              <h2 className="section-title mb-6">Забронувати дату</h2>
              <p className="mb-6 text-[15px] leading-relaxed text-muted-foreground">
                Заповніть коротку анкету — і я зв'яжуся з вами для підтвердження дати та обговорення деталей зйомки. Передоплата 500 грн гарантує резерв часу спеціально для вас.
              </p>
              <ul className="space-y-3 text-sm text-muted-foreground">
                <li className="flex items-start gap-3">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="mt-0.5 h-4 w-4 shrink-0 text-[var(--gold,#c9a96e)]">
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                  Відповідь протягом 2 годин
                </li>
                <li className="flex items-start gap-3">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="mt-0.5 h-4 w-4 shrink-0 text-[var(--gold,#c9a96e)]">
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                  Підбір пакету під ваш бюджет
                </li>
                <li className="flex items-start gap-3">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="mt-0.5 h-4 w-4 shrink-0 text-[var(--gold,#c9a96e)]">
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                  Безкоштовна консультація
                </li>
              </ul>
            </div>

            {/* Form */}
            {submitted ? (
              <div className="flex flex-col items-center justify-center rounded-2xl border border-[var(--gold,#c9a96e)]/30 bg-card/60 p-8 text-center">
                <div className="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-[var(--gold,#c9a96e)] to-[var(--gold-dark,#a8873f)]">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-8 w-8 text-[var(--background,#0a0a0a)]">
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                </div>
                <h3 className="font-serif text-2xl mb-3">Дякую!</h3>
                <p className="text-[14px] text-muted-foreground mb-6">
                  Ваша заявка відкрита в Telegram. Натисніть кнопку «Надіслати», щоб я її отримав.
                </p>
                <button
                  onClick={() => setSubmitted(false)}
                  className="btn-ghost"
                >
                  Заповнити ще раз
                </button>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <label className="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-muted-foreground">
                    Ім&apos;я *
                  </label>
                  <input
                    required
                    type="text"
                    value={form.name}
                    onChange={update('name')}
                    placeholder="Ваше ім'я"
                    className="form-input"
                  />
                </div>
                <div>
                  <label className="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-muted-foreground">
                    Телефон *
                  </label>
                  <input
                    required
                    type="tel"
                    value={form.phone}
                    onChange={update('phone')}
                    placeholder="+380 __ ___ __ __"
                    className="form-input"
                  />
                </div>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <label className="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-muted-foreground">
                      Категорія зйомки
                    </label>
                    <select
                      value={form.category}
                      onChange={update('category')}
                      className="form-input"
                    >
                      <option value="">Оберіть…</option>
                      {categories.map((c) => (
                        <option key={c.id} value={c.title}>{c.title}</option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <label className="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-muted-foreground">
                      Бажана дата
                    </label>
                    <input
                      type="date"
                      value={form.date}
                      onChange={update('date')}
                      className="form-input"
                    />
                  </div>
                </div>
                <div>
                  <label className="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-muted-foreground">
                    Повідомлення
                  </label>
                  <textarea
                    rows={3}
                    value={form.message}
                    onChange={update('message')}
                    placeholder="Опишіть побажання, локацію, кількість людей…"
                    className="form-input resize-none"
                  />
                </div>
                <button type="submit" className="btn-primary w-full">
                  Надіслати заявку
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-4 w-4">
                    <line x1="5" y1="12" x2="19" y2="12" />
                    <polyline points="12 5 19 12 12 19" />
                  </svg>
                </button>
                <p className="text-center text-[11px] text-muted-foreground">
                  Натискаючи кнопку, ви відкриєте Telegram зі заповненим повідомленням
                </p>
              </form>
            )}
          </div>
        </div>
      </div>
    </section>
  );
}
