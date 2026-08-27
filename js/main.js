// ===== LOADER =====
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    if (loader) {
        setTimeout(() => loader.classList.add('hidden'), 1200);
        setTimeout(() => { if (loader.parentNode) loader.parentNode.removeChild(loader); }, 2000);
    }

    // Hero animations
    document.querySelectorAll('.hero-bg img, .category-hero-bg img').forEach(img => {
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', () => img.classList.add('loaded'));
        }
    });

    setTimeout(() => {
        document.querySelectorAll('.hero-title, .hero-sub, .hero-line').forEach(el => {
            el.classList.add('visible');
        });
    }, 200);
});

// ===== HEADER SCROLL =====
const header = document.getElementById('header');
if (header) {
    const onScroll = () => {
        if (window.scrollY > 30) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// ===== MOBILE MENU =====
const burgerBtn = document.getElementById('burgerBtn');
const mobileMenu = document.getElementById('mobileMenu');
const mobileCloseBtn = document.getElementById('mobileCloseBtn');

function openMenu() {
    if (mobileMenu) mobileMenu.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeMenu() {
    if (mobileMenu) mobileMenu.classList.remove('open');
    document.body.style.overflow = '';
}

if (burgerBtn) burgerBtn.addEventListener('click', openMenu);
if (mobileCloseBtn) mobileCloseBtn.addEventListener('click', closeMenu);

// Close mobile menu on link click
document.querySelectorAll('.mobile-nav-link, .mobile-service-card').forEach(link => {
    link.addEventListener('click', closeMenu);
});

// ===== THEME TOGGLE =====
function applyTheme(theme) {
    if (theme === 'light') {
        document.documentElement.classList.add('light');
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
    }
    localStorage.setItem('theme', theme);
    // Update mobile toggle label
    const mobileLabel = document.querySelector('.mobile-theme-toggle .theme-label');
    const mobileIcon = document.querySelector('.mobile-theme-toggle .theme-icon');
    if (mobileLabel) mobileLabel.textContent = theme === 'dark' ? 'Темна тема' : 'Світла тема';
    if (mobileIcon) mobileIcon.textContent = theme === 'dark' ? '☾' : '☀';
}

// Init theme from storage
const storedTheme = localStorage.getItem('theme') || 'dark';
applyTheme(storedTheme);

const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        const current = document.documentElement.classList.contains('light') ? 'light' : 'dark';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });
}
const mobileThemeToggle = document.getElementById('mobileThemeToggle');
if (mobileThemeToggle) {
    mobileThemeToggle.addEventListener('click', () => {
        const current = document.documentElement.classList.contains('light') ? 'light' : 'dark';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });
}

// ===== FAQ ACCORDION =====
document.querySelectorAll('.faq-item').forEach(item => {
    const btn = item.querySelector('.faq-btn');
    const answer = item.querySelector('.faq-answer');
    if (!btn || !answer) return;
    btn.addEventListener('click', () => {
        const isOpen = item.classList.contains('open');
        // Close all
        document.querySelectorAll('.faq-item').forEach(other => {
            other.classList.remove('open');
            const a = other.querySelector('.faq-answer');
            if (a) a.classList.remove('open');
        });
        // Toggle current
        if (!isOpen) {
            item.classList.add('open');
            answer.classList.add('open');
        }
    });
});

// ===== EXTRAS ACCORDION (on category page) =====
document.querySelectorAll('.extra-item').forEach(item => {
    const btn = item.querySelector('.extra-btn');
    const answer = item.querySelector('.extra-answer');
    if (!btn || !answer) return;
    btn.addEventListener('click', () => {
        const isOpen = item.classList.contains('open');
        // Close all
        document.querySelectorAll('.extra-item').forEach(other => {
            other.classList.remove('open');
            const a = other.querySelector('.extra-answer');
            if (a) a.classList.remove('open');
        });
        // Toggle current
        if (!isOpen) {
            item.classList.add('open');
            answer.classList.add('open');
        }
    });
});

// ===== REVEAL ON SCROLL =====
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

// ===== BOOKING FORM =====
const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
    bookingForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(bookingForm);
        const name = formData.get('name') || '';
        const phone = formData.get('phone') || '';
        const category = formData.get('category') || '';
        const date = formData.get('date') || '';
        const message = formData.get('message') || '';

        const text = [
            'Нове бронювання',
            '',
            `Ім'я: ${name}`,
            `Телефон: ${phone}`,
            `Категорія: ${category}`,
            `Дата: ${date}`,
            `Повідомлення: ${message}`,
        ].join('\n');

        const url = `https://t.me/krasnobaevaph?message=${encodeURIComponent(text)}`;
        window.open(url, '_blank');

        // Show success
        const form = document.getElementById('bookingFormFields');
        const success = document.getElementById('bookingSuccess');
        if (form) form.style.display = 'none';
        if (success) success.style.display = 'flex';
    });

    const againBtn = document.getElementById('bookingAgain');
    if (againBtn) {
        againBtn.addEventListener('click', () => {
            const form = document.getElementById('bookingFormFields');
            const success = document.getElementById('bookingSuccess');
            if (form) { form.style.display = 'block'; bookingForm.reset(); }
            if (success) success.style.display = 'none';
        });
    }
}
