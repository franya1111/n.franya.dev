<?php
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/header.php';
?>
<main>
    <!-- HERO -->
    <section class="hero" id="hero">
        <div class="hero-bg">
            <img src="img/hero.jpg" alt="Весільна фотографія">
        </div>
        <div class="hero-decor">
            <div class="hero-decor-circle"></div>
            <div class="hero-decor-circle"></div>
        </div>
        <div class="hero-content">
            <h1 class="hero-title">krasnobaeva</h1>
            <p class="hero-sub">photo &amp; video</p>
            <div class="hero-line"></div>
        </div>
        <a href="#about" class="hero-arrow" aria-label="Scroll down">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
    </section>

    <!-- ABOUT -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-img reveal">
                    <div class="about-img-frame"></div>
                    <img src="img/about.jpg" alt="Фотограф Тетяна Краснобаєва">
                </div>
                <div class="about-text reveal">
                    <span class="section-label">Про мене</span>
                    <h2 class="section-title">Привіт!</h2>
                    <p>Я — Тетяна, фотограф, який допомагає не просто отримати гарні фото, а зберегти справжні емоції та атмосферу моменту.</p>
                    <p>Спеціалізуюсь на індивідуальних, сімейних і весільних зйомках. У своїй роботі поєдную естетику, комфорт та уважність до деталей, щоб у кадрі ви залишились собою — живими, природними та впевненими.</p>
                    <p>Для мене важливо створити не лише красиву картинку, а й сам процес, у якому легко розслабитись і отримувати задоволення від зйомки. Я допомагаю з позуванням, контролюю всі деталі та створюю атмосферу, в якій не потрібно хвилюватися про те, «як стати» чи «що робити».</p>
                    <p>Понад <strong>8 років досвіду</strong> навчили мене головному: найкращі кадри народжуються тоді, коли людина почувається комфортно.</p>
                    <p>Моя мета — фотографії, до яких хочеться повертатися через роки.</p>
                    <a href="https://www.instagram.com/krasnobaeva.ph/" target="_blank" rel="noopener noreferrer" class="about-insta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        @krasnobaeva.ph
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES OVERVIEW -->
    <section class="services-overview" id="services-overview">
        <div class="container">
            <div class="services-overview-header reveal">
                <span class="section-label">Що я пропоную</span>
                <h2 class="section-title">Мої послуги</h2>
                <p class="section-subtitle">Оберіть категорію зйомки — кожна веде на окрему сторінку з описом, прикладами та цінами.</p>
            </div>

            <div class="services-grid">
                <?php foreach ($CATEGORIES as $id => $cat): ?>
                    <a href="category.php?id=<?= $id ?>" class="service-card reveal">
                        <img src="<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['title']) ?>">
                        <div class="service-card-overlay"></div>
                        <div class="service-card-content">
                            <h3><?= htmlspecialchars($cat['title']) ?></h3>
                            <div class="service-card-line"></div>
                            <span class="service-card-cta">Детальніше →</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- REVIEWS -->
    <section class="reviews" id="reviews">
        <div class="container">
            <div class="reviews-header reveal">
                <span class="section-label">Відгуки</span>
                <h2 class="section-title">Що кажуть клієнти</h2>
                <p class="section-subtitle">Понад 200 зйомок за 8 років — кожна зі своєю історією. Ось лише деякі відгуки.</p>
                <a href="https://t.me/krasnobaevaph" target="_blank" rel="noopener noreferrer" class="btn-ghost" style="margin-top: 32px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Залишити відгук
                </a>
            </div>
        </div>

        <div class="reveal">
            <div class="reviews-marquee">
                <?php
                // Duplicate reviews for infinite scroll illusion
                $doubled = array_merge($REVIEWS, $REVIEWS);
                foreach ($doubled as $r):
                ?>
                    <article class="review-card">
                        <div>
                            <div class="review-stars">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <?php endfor; ?>
                            </div>
                            <p class="review-text">&ldquo;<?= htmlspecialchars($r['text']) ?>&rdquo;</p>
                        </div>
                        <div class="review-author">
                            <div class="review-avatar"><?= htmlspecialchars(mb_substr($r['name'], 0, 1)) ?></div>
                            <div>
                                <p class="review-name"><?= htmlspecialchars($r['name']) ?></p>
                                <p class="review-role">Клієнт</p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="container">
            <p class="reviews-hint">↑ Наведіть курсор, щоб зупинити прокрутку</p>
        </div>
    </section>

    <!-- BOOKING -->
    <section class="booking" id="booking">
        <div class="booking-card reveal">
            <div class="booking-glow tl"></div>
            <div class="booking-glow br"></div>
            <div class="booking-grid">
                <div>
                    <span class="section-label">Анкета бронювання</span>
                    <h2 class="section-title">Забронувати дату</h2>
                    <p style="color: var(--text-muted); font-size: 15px; line-height: 1.7;">
                        Заповніть коротку анкету — і я зв'яжуся з вами для підтвердження дати та обговорення деталей зйомки. Передоплата 500 грн гарантує резерв часу спеціально для вас.
                    </p>
                    <ul class="booking-info-list">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Відповідь протягом 2 годин</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Підбір пакету під ваш бюджет</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Безкоштовна консультація</li>
                    </ul>
                </div>

                <div id="bookingFormFields">
                    <form id="bookingForm" method="post">
                        <div class="form-group">
                            <label class="form-label">Ім'я *</label>
                            <input type="text" name="name" required placeholder="Ваше ім'я" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Телефон *</label>
                            <input type="tel" name="phone" required placeholder="+380 __ ___ __ __" class="form-input">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Категорія зйомки</label>
                                <select name="category" class="form-select">
                                    <option value="">Оберіть…</option>
                                    <?php foreach ($CATEGORIES as $id => $c): ?>
                                        <option value="<?= htmlspecialchars($c['title']) ?>"><?= htmlspecialchars($c['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Бажана дата</label>
                                <input type="date" name="date" class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Повідомлення</label>
                            <textarea name="message" rows="3" placeholder="Опишіть побажання, локацію, кількість людей…" class="form-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%;">
                            Надіслати заявку
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                        <p style="text-align:center; font-size:11px; color: var(--text-muted); margin-top: 12px;">
                            Натискаючи кнопку, ви відкриєте Telegram зі заповненим повідомленням
                        </p>
                    </form>
                </div>

                <div id="bookingSuccess" class="form-success" style="display: none;">
                    <div class="form-success-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h3>Дякую!</h3>
                    <p>Ваша заявка відкрита в Telegram. Натисніть кнопку «Надіслати», щоб я її отримав.</p>
                    <button type="button" id="bookingAgain" class="btn-ghost">Заповнити ще раз</button>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq" id="faq">
        <div class="faq-list">
            <div class="faq-header reveal">
                <span class="section-label">FAQ</span>
                <h2 class="section-title">Питання та відповіді</h2>
                <p class="section-subtitle">Зібрав найчастіші питання — якщо вашого тут немає, напишіть мені особисто.</p>
            </div>

            <?php foreach ($FAQS as $faq): ?>
                <div class="faq-item reveal">
                    <button class="faq-btn" type="button">
                        <span><?= htmlspecialchars($faq['q']) ?></span>
                        <span class="faq-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                    </button>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars($faq['a']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- CONTACTS -->
    <section class="contacts" id="contacts">
        <div class="container-narrow">
            <div class="contacts-inner reveal">
                <span class="section-label">Зв'язок</span>
                <h2 class="section-title">Контакти</h2>
                <p style="color: var(--text-muted); font-size: 15px; line-height: 1.7;">
                    Для зв'язку зі мною напишіть мені в соціальних мережах або зателефонуйте. Завжди на зв'язку.
                </p>
                <div class="contacts-btns">
                    <a href="tel:+380938383871" class="btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Зв'язатися зі мною
                    </a>
                    <a href="https://www.instagram.com/krasnobaeva.ph/" target="_blank" rel="noopener noreferrer" class="btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        Instagram
                    </a>
                    <a href="https://t.me/krasnobaevaph" target="_blank" rel="noopener noreferrer" class="btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Telegram
                    </a>
                    <a href="https://wa.me/380938383871" target="_blank" rel="noopener noreferrer" class="btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
