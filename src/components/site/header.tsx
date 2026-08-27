'use client';

import { useEffect, useState } from "react";
import { categories } from "@/lib/site-data";

type Props = {
  onNavigateCategory: (id: string) => void;
  onNavigateHome: (section?: string) => void;
  isHome: boolean;
};

export function Header({ onNavigateCategory, onNavigateHome, isHome }: Props) {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [servicesOpen, setServicesOpen] = useState(false);
  const [theme, setTheme] = useState<'dark' | 'light'>(() => {
    if (typeof window === 'undefined') return 'dark';
    const stored = localStorage.getItem('theme') as 'dark' | 'light' | null;
    return stored || 'dark';
  });

  // Apply theme to documentElement whenever theme changes
  useEffect(() => {
    document.documentElement.classList.toggle('light', theme === 'light');
    document.documentElement.classList.toggle('dark', theme === 'dark');
  }, [theme]);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 30);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  useEffect(() => {
    if (mobileOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  }, [mobileOpen]);

  const toggleTheme = () => {
    const next = theme === 'dark' ? 'light' : 'dark';
    setTheme(next);
    document.documentElement.classList.toggle('light', next === 'light');
    document.documentElement.classList.toggle('dark', next === 'dark');
    localStorage.setItem('theme', next);
  };

  const goHome = (section?: string) => {
    setMobileOpen(false);
    onNavigateHome(section);
  };

  const goCategory = (id: string) => {
    setMobileOpen(false);
    setServicesOpen(false);
    onNavigateCategory(id);
  };

  return (
    <>
      <header
        className={`fixed top-0 left-0 right-0 z-[100] transition-all duration-300 ${
          scrolled
            ? 'glass border-b border-border/40 py-0'
            : 'bg-transparent py-0'
        }`}
      >
        <div className="mx-auto flex h-[72px] max-w-[1200px] items-center justify-between px-6 md:h-[88px]">
          {/* Logo */}
          <button
            onClick={() => goHome()}
            className="group relative font-serif text-[22px] tracking-[0.25em] transition-colors hover:text-[var(--gold,#c9a96e)]"
            aria-label="krasnobaeva home"
          >
            krasnobaeva
            <span className="absolute -bottom-1 left-0 h-px w-0 bg-[var(--gold,#c9a96e)] transition-all duration-500 group-hover:w-full" />
          </button>

          {/* Desktop nav */}
          <nav className="hidden items-center gap-1.5 lg:flex">
            <button
              onClick={() => goHome()}
              className="px-3.5 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted-foreground transition-colors hover:text-foreground"
            >
              Головна
            </button>

            <div
              className="relative"
              onMouseEnter={() => setServicesOpen(true)}
              onMouseLeave={() => setServicesOpen(false)}
            >
              <button className="flex items-center gap-1.5 px-3.5 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted-foreground transition-colors hover:text-foreground">
                Послуги
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth={2}
                  className={`h-3 w-3 transition-transform ${servicesOpen ? 'rotate-180' : ''}`}
                >
                  <polyline points="6 9 12 15 18 9" />
                </svg>
              </button>

              {servicesOpen && (
                <div className="absolute left-1/2 top-full -translate-x-1/2 pt-3">
                  <div className="grid w-[480px] grid-cols-2 gap-1 rounded-xl border border-border/60 bg-card/95 p-2 shadow-2xl backdrop-blur-xl">
                    {categories.map((cat) => (
                      <button
                        key={cat.id}
                        onClick={() => goCategory(cat.id)}
                        className="flex items-center gap-3 rounded-lg p-2 text-left transition-colors hover:bg-secondary/60"
                      >
                        <div className="h-10 w-10 shrink-0 overflow-hidden rounded-md">
                          { }
                          <img
                            src={cat.heroImage}
                            alt={cat.title}
                            className="h-full w-full object-cover"
                          />
                        </div>
                        <span className="font-serif text-sm tracking-wide text-foreground">
                          {cat.title}
                        </span>
                      </button>
                    ))}
                  </div>
                </div>
              )}
            </div>

            <button
              onClick={() => goHome('reviews')}
              className="px-3.5 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted-foreground transition-colors hover:text-foreground"
            >
              Відгуки
            </button>
            <button
              onClick={() => goHome('booking')}
              className="px-3.5 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted-foreground transition-colors hover:text-foreground"
            >
              Бронювання
            </button>
            <button
              onClick={() => goHome('faq')}
              className="px-3.5 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted-foreground transition-colors hover:text-foreground"
            >
              Питання
            </button>
            <button
              onClick={() => goHome('contacts')}
              className="px-3.5 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted-foreground transition-colors hover:text-foreground"
            >
              Контакти
            </button>

            <button
              onClick={toggleTheme}
              className="ml-2 flex h-9 w-9 items-center justify-center rounded-full border border-border/60 text-foreground transition-colors hover:border-[var(--gold,#c9a96e)] hover:text-[var(--gold,#c9a96e)]"
              aria-label="Toggle theme"
            >
              {theme === 'dark' ? (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-4 w-4">
                  <circle cx="12" cy="12" r="5" />
                  <line x1="12" y1="1" x2="12" y2="3" />
                  <line x1="12" y1="21" x2="12" y2="23" />
                  <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                  <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                  <line x1="1" y1="12" x2="3" y2="12" />
                  <line x1="21" y1="12" x2="23" y2="12" />
                  <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                  <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                </svg>
              ) : (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-4 w-4">
                  <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                </svg>
              )}
            </button>
          </nav>

          {/* Mobile burger */}
          <button
            onClick={() => setMobileOpen(true)}
            className="flex h-10 w-10 items-center justify-center lg:hidden"
            aria-label="Open menu"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-6 w-6">
              <line x1="3" y1="6" x2="21" y2="6" />
              <line x1="3" y1="12" x2="21" y2="12" />
              <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
          </button>
        </div>
      </header>

      {/* Mobile menu */}
      <div
        className={`fixed inset-0 z-[200] transform bg-background transition-transform duration-500 lg:hidden ${
          mobileOpen ? 'translate-x-0' : 'translate-x-full'
        }`}
      >
        <div className="flex h-[72px] items-center justify-between px-6">
          <span className="font-serif text-[20px] tracking-[0.25em]">krasnobaeva</span>
          <button
            onClick={() => setMobileOpen(false)}
            className="flex h-10 w-10 items-center justify-center"
            aria-label="Close menu"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-6 w-6">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        <nav className="flex flex-col gap-1 px-6 py-4">
          <button
            onClick={() => goHome()}
            className="border-b border-border/50 py-4 text-left font-serif text-2xl tracking-wide"
          >
            Головна
          </button>

          <div className="border-b border-border/50 py-4">
            <p className="mb-3 text-[10px] font-semibold uppercase tracking-[0.3em] text-muted-foreground">Послуги</p>
            <div className="grid grid-cols-2 gap-2">
              {categories.map((cat) => (
                <button
                  key={cat.id}
                  onClick={() => goCategory(cat.id)}
                  className="flex items-center gap-2 rounded-lg bg-secondary/40 p-2 text-left transition-colors hover:bg-secondary"
                >
                  { }
                  <img
                    src={cat.heroImage}
                    alt={cat.title}
                    className="h-10 w-10 shrink-0 rounded-md object-cover"
                  />
                  <span className="font-serif text-sm">{cat.title}</span>
                </button>
              ))}
            </div>
          </div>

          <button
            onClick={() => goHome('reviews')}
            className="border-b border-border/50 py-4 text-left font-serif text-2xl tracking-wide"
          >
            Відгуки
          </button>
          <button
            onClick={() => goHome('booking')}
            className="border-b border-border/50 py-4 text-left font-serif text-2xl tracking-wide"
          >
            Бронювання
          </button>
          <button
            onClick={() => goHome('faq')}
            className="border-b border-border/50 py-4 text-left font-serif text-2xl tracking-wide"
          >
            Питання
          </button>
          <button
            onClick={() => goHome('contacts')}
            className="border-b border-border/50 py-4 text-left font-serif text-2xl tracking-wide"
          >
            Контакти
          </button>

          <button
            onClick={toggleTheme}
            className="mt-6 flex items-center gap-3 text-left"
          >
            <span className="flex h-10 w-10 items-center justify-center rounded-full border border-border">
              {theme === 'dark' ? '☀' : '☾'}
            </span>
            <span className="font-serif text-xl">{theme === 'dark' ? 'Світла тема' : 'Темна тема'}</span>
          </button>
        </nav>
      </div>
    </>
  );
}
