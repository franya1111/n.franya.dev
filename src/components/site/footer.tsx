export function Footer({ onHome }: { onHome: () => void }) {
  return (
    <footer className="mt-auto border-t border-border/60 bg-background">
      <div className="mx-auto max-w-[1200px] px-6 py-10">
        <div className="flex flex-col items-center justify-between gap-6 md:flex-row">
          <div className="flex items-center gap-4">
            <a
              href="https://www.instagram.com/krasnobaeva.ph/"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="Instagram"
              className="flex h-10 w-10 items-center justify-center rounded-full border border-border/60 text-muted-foreground transition-all hover:border-[var(--gold,#c9a96e)] hover:text-[var(--gold,#c9a96e)]"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-4 w-4">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
              </svg>
            </a>
            <a
              href="https://t.me/krasnobaevaph"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="Telegram"
              className="flex h-10 w-10 items-center justify-center rounded-full border border-border/60 text-muted-foreground transition-all hover:border-[var(--gold,#c9a96e)] hover:text-[var(--gold,#c9a96e)]"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="h-4 w-4">
                <line x1="22" y1="2" x2="11" y2="13" />
                <polygon points="22 2 15 22 11 13 2 9 22 2" />
              </svg>
            </a>
          </div>

          <p className="text-center text-[11px] uppercase tracking-[0.25em] text-muted-foreground">
            © {new Date().getFullYear()} Tetiana Krasnobaeva Photo &amp; Video
          </p>

          <button
            onClick={onHome}
            className="group relative font-serif text-[18px] tracking-[0.25em] transition-colors hover:text-[var(--gold,#c9a96e)]"
            aria-label="Back to top"
          >
            krasnobaeva
            <span className="absolute -bottom-1 left-0 h-px w-0 bg-[var(--gold,#c9a96e)] transition-all duration-500 group-hover:w-full" />
          </button>
        </div>
      </div>
    </footer>
  );
}
