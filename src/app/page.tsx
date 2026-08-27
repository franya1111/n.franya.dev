'use client';

import { useEffect, useRef, useState } from 'react';
import { Header } from '@/components/site/header';
import { Loader } from '@/components/site/loader';
import { Hero } from '@/components/site/hero';
import { About } from '@/components/site/about';
import { Services } from '@/components/site/services';
import { Reviews } from '@/components/site/reviews';
import { Faq } from '@/components/site/faq';
import { BookingForm } from '@/components/site/booking-form';
import { Contacts } from '@/components/site/contacts';
import { Footer } from '@/components/site/footer';
import { CategoryDetailView } from '@/components/site/category-detail-view';
import { categories } from '@/lib/site-data';

type View = { kind: 'home' } | { kind: 'category'; id: string };

export default function Home() {
  const [view, setView] = useState<View>({ kind: 'home' });
  const bookingRef = useRef<HTMLDivElement>(null);

  // Update document title based on view
  useEffect(() => {
    if (view.kind === 'category') {
      const cat = categories.find((c) => c.id === view.id);
      if (cat) {
        document.title = `${cat.title} — Тетяна Краснобаєва`;
      }
    } else {
      document.title = 'Тетяна Краснобаєва — Фотограф | Фото та Відео';
    }
  }, [view]);

  const goToCategory = (id: string) => {
    setView({ kind: 'category', id });
    window.scrollTo({ top: 0, behavior: 'instant' as ScrollBehavior });
  };

  const goToHome = (section?: string) => {
    setView({ kind: 'home' });
    // wait for render then scroll to section
    setTimeout(() => {
      if (section) {
        const el = document.getElementById(section);
        if (el) {
          el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    }, 50);
  };

  const scrollToBooking = () => {
    setView({ kind: 'home' });
    setTimeout(() => {
      const el = document.getElementById('booking');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 80);
  };

  return (
    <div className="relative flex min-h-screen flex-col bg-background text-foreground grain-overlay">
      <Loader />
      <Header
        isHome={view.kind === 'home'}
        onNavigateCategory={goToCategory}
        onNavigateHome={goToHome}
      />

      <main className="flex-1">
        {view.kind === 'home' ? (
          <>
            <Hero onExplore={() => {
              const el = document.getElementById('about');
              if (el) el.scrollIntoView({ behavior: 'smooth' });
            }} />
            <About />
            <Services onSelectCategory={goToCategory} />
            <Reviews />
            <BookingForm />
            <Faq />
            <Contacts />
          </>
        ) : (
          <CategoryDetailView
            key={view.id}
            category={categories.find((c) => c.id === view.id)!}
            onBack={() => goToHome()}
            onSelectCategory={goToCategory}
            onBookNow={scrollToBooking}
          />
        )}
      </main>

      <Footer onHome={() => goToHome()} />
    </div>
  );
}
