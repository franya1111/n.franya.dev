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

// ===== BOOKING FORM (on booking.php) =====
const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
    const submitBtn = bookingForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

    // Елементи
    const categorySelect = document.getElementById('bookingCategory');
    const packageSelect = document.getElementById('bookingPackage');
    const participantsSelect = document.getElementById('bookingParticipants');
    const participantsOtherInput = document.getElementById('bookingParticipantsOther');
    const isRegularCheckbox = document.getElementById('bookingIsRegular');
    const discountSummary = document.getElementById('discountSummary');
    const discountPackageRow = document.getElementById('discountPackageRow');
    const discountPriceOld = document.getElementById('discountPriceOld');
    const discountPriceNew = document.getElementById('discountPriceNew');
    const discountAmount = document.getElementById('discountAmount');

    const CATEGORIES_DATA = window.CATEGORIES_DATA || {};
    const PARTICIPANTS_OPTIONS = window.PARTICIPANTS_OPTIONS || {};
    const PRE_CAT = window.PRE_CAT || '';
    const PRE_PKG = window.PRE_PKG ?? -1;

    // === Спосіб зв'язку ===
    const contactMethodBtns = document.querySelectorAll('.contact-method-btn');
    const contactMethodInput = document.getElementById('contactMethod');
    const contactValueInput = document.getElementById('contactValue');
    const contactLabel = document.getElementById('contactLabel');
    const contactHelp = document.getElementById('contactHelp');

    const contactConfig = {
        phone:    {
            label: 'Номер телефону *',
            placeholder: '+380 50 123 45 67',
            type: 'tel',
            help: 'Формат: +380 50 123 45 67',
            pattern: '^[+]?[0-9\\s\\-()]{7,20}$',
            validationMsg: 'Введіть коректний номер телефону',
        },
        telegram: {
            label: 'Ваш нік в Telegram *',
            placeholder: '@username',
            type: 'text',
            help: 'Без пробілів, напр.: @krasnobaeva',
            pattern: '^@?[a-zA-Z0-9_]{4,32}$',
            validationMsg: 'Введіть коректний нік Telegram (4-32 символи, @/_/літери/цифри)',
        },
        instagram:{
            label: 'Ваш нік в Instagram *',
            placeholder: '@username',
            type: 'text',
            help: 'Без пробілів, напр.: @krasnobaeva.ph',
            pattern: '^@?[a-zA-Z0-9._]{1,30}$',
            validationMsg: 'Введіть коректний нік Instagram (1-30 символи, @/._/літери/цифри)',
        },
    };

    contactMethodBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const method = btn.getAttribute('data-method');
            contactMethodBtns.forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-checked', 'false');
            });
            btn.classList.add('active');
            btn.setAttribute('aria-checked', 'true');
            contactMethodInput.value = method;
            const cfg = contactConfig[method] || contactConfig.phone;
            if (contactLabel) contactLabel.textContent = cfg.label;
            if (contactValueInput) {
                contactValueInput.type = cfg.type;
                contactValueInput.placeholder = cfg.placeholder;
                contactValueInput.value = '';
                contactValueInput.setAttribute('pattern', cfg.pattern || '');
                contactValueInput.setAttribute('title', cfg.validationMsg || '');
                contactValueInput.removeAttribute('maxlength');
                if (method === 'phone') {
                    contactValueInput.setAttribute('maxlength', '20');
                    contactValueInput.setAttribute('autocomplete', 'tel');
                } else {
                    contactValueInput.setAttribute('maxlength', '32');
                    contactValueInput.removeAttribute('autocomplete');
                }
            }
            if (contactHelp) contactHelp.textContent = cfg.help;
        });
    });

    // === Парсер цін ===
    function parsePrice(str) {
        if (!str) return 0;
        const m = String(str).match(/[\d\s]+/);
        if (!m) return 0;
        return parseInt(m[0].replace(/\s/g, ''), 10) || 0;
    }
    function formatPrice(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' грн';
    }

    // === Оновити пакети при зміні категорії ===
    function updatePackages() {
        if (!categorySelect) return;

        const selected = categorySelect.options[categorySelect.selectedIndex];
        const catId = selected ? selected.getAttribute('data-cat-id') : null;
        const cat = catId ? CATEGORIES_DATA[catId] : null;

        if (packageSelect) {
            packageSelect.innerHTML = '';
            if (!cat || !cat.packages || !cat.packages.length) {
                packageSelect.disabled = true;
                packageSelect.innerHTML = '<option value="">Спочатку вид зйомки…</option>';
            } else {
                packageSelect.disabled = false;
                packageSelect.innerHTML = '<option value="">Оберіть пакет…</option>';
                cat.packages.forEach((pkg, i) => {
                    const opt = document.createElement('option');
                    opt.value = pkg.name;
                    opt.textContent = `${pkg.name} — ${pkg.price} (${pkg.duration})`;
                    opt.setAttribute('data-price', pkg.price);
                    opt.setAttribute('data-duration', pkg.duration);
                    packageSelect.appendChild(opt);
                });
            }
        }
        updateParticipants(catId);
        updateDiscount();
    }

    // === Оновити варіанти кількості учасників ===
    function updateParticipants(catId) {
        if (!participantsSelect) return;
        const opts = PARTICIPANTS_OPTIONS[catId] || [];
        const currentValue = participantsSelect.value;

        participantsSelect.disabled = false;
        participantsSelect.innerHTML = '<option value="">Оберіть кількість…</option>';
        opts.forEach(opt => {
            const o = document.createElement('option');
            o.value = opt;
            o.textContent = opt;
            participantsSelect.appendChild(o);
        });

        // Додаємо опцію "Інше"
        const otherOpt = document.createElement('option');
        otherOpt.value = '__other__';
        otherOpt.textContent = 'Інше (ввести вручну)…';
        participantsSelect.appendChild(otherOpt);

        if (opts.indexOf(currentValue) !== -1) {
            participantsSelect.value = currentValue;
        } else {
            participantsSelect.value = '';
        }
        hideParticipantsOther();
    }

    // === Показати/сховати текстове поле "Інше" ===
    function showParticipantsOther() {
        if (participantsOtherInput) {
            participantsOtherInput.style.display = 'block';
            participantsOtherInput.required = true;
            participantsOtherInput.focus();
        }
    }
    function hideParticipantsOther() {
        if (participantsOtherInput) {
            participantsOtherInput.style.display = 'none';
            participantsOtherInput.required = false;
            participantsOtherInput.value = '';
        }
    }
    if (participantsSelect) {
        participantsSelect.addEventListener('change', () => {
            if (participantsSelect.value === '__other__') {
                showParticipantsOther();
            } else {
                hideParticipantsOther();
            }
        });
    }

    // === Оновити блок розрахунку знижки ===
    function updateDiscount() {
        if (!isRegularCheckbox || !isRegularCheckbox.checked) {
            if (discountSummary) discountSummary.style.display = 'none';
            return;
        }

        // Отримуємо вибраний пакет
        let selectedPkg = null;
        if (packageSelect && !packageSelect.disabled) {
            selectedPkg = packageSelect.options[packageSelect.selectedIndex];
        }

        // Якщо пакет предзаповнений (hidden input), шукаємо ціну там
        let priceStr = '';
        let durationStr = '';
        let pkgName = '';

        if (selectedPkg) {
            priceStr = selectedPkg.getAttribute('data-price') || '';
            durationStr = selectedPkg.getAttribute('data-duration') || '';
            pkgName = selectedPkg.value || '';
        } else {
            // Спробуємо взяти з hidden inputs (для предзаповненого пакета)
            const hiddenPrice = document.querySelector('input[name="package_price"]');
            const hiddenDuration = document.querySelector('input[name="package_duration"]');
            const hiddenName = document.querySelector('input[name="package"]');
            if (hiddenPrice) priceStr = hiddenPrice.value;
            if (hiddenDuration) durationStr = hiddenDuration.value;
            if (hiddenName) pkgName = hiddenName.value;
        }

        if (!pkgName) {
            if (discountSummary) discountSummary.style.display = 'none';
            return;
        }

        const price = parsePrice(priceStr);
        const newPrice = Math.round(price * 0.9);
        const discount = price - newPrice;

        if (discountPackageRow) {
            discountPackageRow.textContent = `${pkgName} · ${durationStr || ''}`.trim().replace(/·\s*$/, '');
        }
        if (discountPriceOld) discountPriceOld.textContent = formatPrice(price);
        if (discountAmount) discountAmount.textContent = '− ' + formatPrice(discount);
        if (discountPriceNew) discountPriceNew.textContent = formatPrice(newPrice);
        if (discountSummary) discountSummary.style.display = 'block';
    }

    if (categorySelect) categorySelect.addEventListener('change', updatePackages);
    if (packageSelect) packageSelect.addEventListener('change', updateDiscount);
    if (isRegularCheckbox) isRegularCheckbox.addEventListener('change', updateDiscount);

    // Ініціалізація при завантаженні:
    // Якщо pre_cat предзаповнена — updateDiscount вже може запуститись
    // (participants вже заповнені через PHP, package select/hidden теж на місці)
    if (PRE_CAT) {
        updateDiscount();
    }

    // === Submit ===
    bookingForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(bookingForm);

        // Валідація: якщо selected "Інше" — повинно бути заповнене
        let participantsValue = (formData.get('participants') || '').trim();
        const participantsOther = (formData.get('participants_other') || '').trim();
        if (participantsValue === '__other__') {
            if (!participantsOther) {
                alert('Введіть кількість учасників у полі «Інше».');
                if (participantsOtherInput) participantsOtherInput.focus();
                return;
            }
            participantsValue = participantsOther;
        }

        // Валідація контактів
        const contactMethod = (formData.get('contact_method') || 'phone').trim();
        const contactValue = (formData.get('contact_value') || '').trim();
        const cfg = contactConfig[contactMethod] || contactConfig.phone;
        if (cfg.pattern) {
            const re = new RegExp(cfg.pattern);
            if (!re.test(contactValue)) {
                alert(cfg.validationMsg);
                if (contactValueInput) contactValueInput.focus();
                return;
            }
        }

        const payload = {
            name: (formData.get('name') || '').trim(),
            category: (formData.get('category') || '').trim(),
            package: (formData.get('package') || '').trim(),
            participants: participantsValue,
            date: (formData.get('date') || '').trim(),
            time: (formData.get('time') || '').trim(),
            is_regular: formData.get('is_regular') === '1',
            contact_method: contactMethod,
            contact_value: contactValue,
            message: (formData.get('message') || '').trim(),
        };

        // Розрахунок знижки
        if (payload.is_regular && payload.package) {
            let priceStr = '';
            const selectedPkg = packageSelect && !packageSelect.disabled
                ? packageSelect.options[packageSelect.selectedIndex]
                : null;
            if (selectedPkg) {
                priceStr = selectedPkg.getAttribute('data-price') || '';
            } else {
                const hiddenPrice = document.querySelector('input[name="package_price"]');
                if (hiddenPrice) priceStr = hiddenPrice.value;
            }
            const price = parsePrice(priceStr);
            const newPrice = Math.round(price * 0.9);
            payload.discount_info = `Постійний клієнт −10%: ${formatPrice(price)} → ${formatPrice(newPrice)}`;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Надсилаємо…';
        }

        try {
            const response = await fetch('api/booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const result = await response.json();

            if (result.success) {
                const form = bookingForm;
                const success = document.getElementById('bookingSuccess');
                if (form) form.style.display = 'none';
                if (success) success.style.display = 'flex';
                if (success) {
                    setTimeout(() => {
                        success.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
            } else {
                alert(result.message || 'Помилка відправки. Спробуйте пізніше або напишіть у Telegram.');
            }
        } catch (err) {
            console.error('Booking submit error:', err);
            alert('Не вдалося з\'єднатись з сервером. Перевірте інтернет та спробуйте ще раз.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    });
}
