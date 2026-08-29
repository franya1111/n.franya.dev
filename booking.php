<?php
require_once __DIR__ . '/includes/data.php';

// Авто-визначення категорії з URL (?cat=individual)
$pre_cat = $_GET['cat'] ?? '';
$pre_pkg = (int)($_GET['pkg'] ?? -1);  // індекс пакета
$pre_category_data = null;
$pre_package_data = null;
if ($pre_cat && isset($CATEGORIES[$pre_cat])) {
    $pre_category_data = $CATEGORIES[$pre_cat];
    if ($pre_pkg >= 0 && isset($pre_category_data['packages'][$pre_pkg])) {
        $pre_package_data = $pre_category_data['packages'][$pre_pkg];
    }
}

// Варіанти кількості учасників за категоріями
$participants_options = [
    'individual' => ['1 человек', '2 человека', '3 человека', '4 человека', '5 человек', '6 и более'],
    'wedding'    => ['до 10 человек', '11–30 человек', '31–50 человек', '51–100 человек', 'более 100 человек'],
    'family'     => ['2 человека', '3 человека', '4 человека', '5 человек', '6 и более'],
    'love-story' => ['2 человека', '3 человека', '4 человека', '5 человек', '6 и более'],
    'pregnancy'  => ['1 человек', '2 человека', '3 человека', '4 человека', '5 человек', '6 и более'],
    'birthday'   => ['1–5 человек', '6–10 человек', '11–20 человек', 'более 20 человек'],
];

$page_title = 'Анкета бронювання';
require_once __DIR__ . '/includes/header.php';
?>
<main>
    <section class="booking-page" id="booking">
        <div class="container">
            <div class="booking-page-card reveal visible">
                <!-- Заголовок -->
                <div class="booking-page-header">
                    <span class="section-label">Анкета бронювання</span>
                    <h1 class="booking-page-title"><?= htmlspecialchars($SETTINGS['booking']['title'] ?? 'Забронувати зйомку') ?></h1>
                    <?php if ($pre_category_data): ?>
                        <p class="booking-page-subtitle">
                            Ви бронюєте:
                            <strong><?= htmlspecialchars($pre_category_data['title']) ?></strong>
                            <?php if ($pre_package_data): ?>
                                <span class="dot">·</span>
                                Пакет <strong><?= htmlspecialchars($pre_package_data['name']) ?></strong>
                                <span class="heart">♥</span>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                        <p class="booking-page-subtitle"><?= htmlspecialchars($SETTINGS['booking']['subtitle_default'] ?? 'Заповніть анкету — і я зв\'яжуся з вами найближчим часом.') ?></p>
                    <?php endif; ?>
                    <a href="javascript:history.back()" class="booking-close" aria-label="Закрити">×</a>
                </div>

                <!-- Форма -->
                <form id="bookingForm" method="post" class="booking-form">
                    <!-- Ім'я -->
                    <div class="form-group">
                        <label class="form-label">Ім'я клієнта *</label>
                        <input type="text" name="name" required placeholder="Як до вас звертатись?" class="form-input">
                    </div>

                    <!-- Вид зйомки + Пакет (2 колонки) -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Вид зйомки</label>
                            <?php if ($pre_category_data): ?>
                                <input type="text" value="<?= htmlspecialchars($pre_category_data['title']) ?>" class="form-input form-input-locked" readonly>
                                <input type="hidden" name="category" value="<?= htmlspecialchars($pre_category_data['title']) ?>">
                                <input type="hidden" name="category_id" value="<?= htmlspecialchars($pre_cat) ?>">
                                <span class="form-locked-hint">🔒 Замкнено (визначається сторінкою)</span>
                            <?php else: ?>
                                <select name="category" id="bookingCategory" class="form-select" required>
                                    <option value="">Оберіть…</option>
                                    <?php foreach ($CATEGORIES as $id => $c): ?>
                                        <option value="<?= htmlspecialchars($c['title']) ?>" data-cat-id="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($c['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Вибір пакета</label>
                            <?php if ($pre_package_data): ?>
                                <input type="text" value="<?= htmlspecialchars($pre_package_data['name']) ?>" class="form-input form-input-locked" readonly>
                                <input type="hidden" name="package" value="<?= htmlspecialchars($pre_package_data['name']) ?>">
                                <input type="hidden" name="package_index" value="<?= $pre_pkg ?>">
                                <input type="hidden" name="package_price" value="<?= htmlspecialchars($pre_package_data['price']) ?>">
                                <input type="hidden" name="package_duration" value="<?= htmlspecialchars($pre_package_data['duration']) ?>">
                            <?php else: ?>
                                <select name="package" id="bookingPackage" class="form-select" <?= $pre_category_data ? '' : 'disabled' ?>>
                                    <?php if ($pre_category_data): ?>
                                        <?php foreach ($pre_category_data['packages'] as $i => $pkg): ?>
                                            <option value="<?= htmlspecialchars($pkg['name']) ?>" data-price="<?= htmlspecialchars($pkg['price']) ?>" data-duration="<?= htmlspecialchars($pkg['duration']) ?>">
                                                <?= htmlspecialchars($pkg['name']) ?> — <?= htmlspecialchars($pkg['price']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Спочатку вид зйомки…</option>
                                    <?php endif; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Кількість учасників -->
                    <div class="form-group">
                        <label class="form-label">Кількість учасників</label>
                        <select name="participants" id="bookingParticipants" class="form-select" required>
                            <option value="">Оберіть кількість…</option>
                            <?php
                            $current_cat_id = $pre_cat;
                            $opts = $participants_options[$current_cat_id] ?? [];
                            foreach ($opts as $opt):
                            ?>
                                <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="form-help">Враховуйте всіх учасників — дорослих та дітей.</p>
                    </div>

                    <!-- Дата + Час (2 колонки) -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Бажана дата</label>
                            <input type="date" name="date" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Бажаний час</label>
                            <input type="time" name="time" class="form-input">
                        </div>
                    </div>

                    <!-- Чекбокс постійного клієнта -->
                    <div class="booking-discount" id="bookingDiscountBlock">
                        <label class="discount-checkbox">
                            <input type="checkbox" name="is_regular" id="bookingIsRegular" value="1">
                            <span class="discount-checkbox-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span class="discount-checkbox-label">
                                Я постійний клієнт(-ка) 🌸
                                <span class="discount-badge">−10%</span>
                            </span>
                        </label>

                        <!-- Розрахунок вартості -->
                        <div class="discount-summary" id="discountSummary" style="display:none;">
                            <div class="discount-summary-row">
                                <span class="summary-label" id="discountPackageRow">Пакет · тривалість</span>
                                <span class="summary-value" id="discountPriceOld">—</span>
                            </div>
                            <div class="discount-summary-row discount-row-discount">
                                <span class="summary-label">Знижка постійного клієнта (−10%)</span>
                                <span class="summary-value summary-discount" id="discountAmount">—</span>
                            </div>
                            <div class="discount-summary-divider"></div>
                            <div class="discount-summary-row discount-summary-total">
                                <span class="summary-label">До сплати:</span>
                                <span class="summary-value summary-total" id="discountPriceNew">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- Спосіб зв'язку -->
                    <div class="form-group">
                        <label class="form-label">Переважний спосіб зв'язку</label>
                        <div class="contact-method-group" role="radiogroup">
                            <button type="button" class="contact-method-btn active" data-method="phone" role="radio" aria-checked="true">
                                📞 Телефон
                            </button>
                            <button type="button" class="contact-method-btn" data-method="telegram" role="radio" aria-checked="false">
                                ✈️ Telegram
                            </button>
                            <button type="button" class="contact-method-btn" data-method="instagram" role="radio" aria-checked="false">
                                📷 Instagram
                            </button>
                        </div>
                        <input type="hidden" name="contact_method" id="contactMethod" value="phone">
                    </div>

                    <!-- Контактні дані (динамічне поле) -->
                    <div class="form-group">
                        <label class="form-label" id="contactLabel">Номер телефону *</label>
                        <input type="tel" name="contact_value" id="contactValue" required placeholder="+380 __ ___ __ __" class="form-input">
                    </div>

                    <!-- Побажання -->
                    <div class="form-group">
                        <label class="form-label">Побажання до зйомки</label>
                        <textarea name="message" rows="3" placeholder="Розкажіть про вашу ідею..." class="form-textarea"></textarea>
                    </div>

                    <button type="submit" class="btn-primary booking-submit">
                        <?= htmlspecialchars($SETTINGS['booking']['submit_btn_text'] ?? 'Надіслати запит 💌') ?>
                    </button>

                    <!-- Дані для JS -->
                    <script>
                    window.CATEGORIES_DATA = <?= json_encode($CATEGORIES, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
                    window.PARTICIPANTS_OPTIONS = <?= json_encode($participants_options, JSON_UNESCAPED_UNICODE) ?>;
                    window.PRE_CAT = <?= json_encode($pre_cat, JSON_UNESCAPED_UNICODE) ?>;
                    window.PRE_PKG = <?= json_encode($pre_pkg, JSON_UNESCAPED_UNICODE) ?>;
                    </script>
                </form>

                <!-- Повідомлення про успіх -->
                <div id="bookingSuccess" class="form-success" style="display: none;">
                    <div class="form-success-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h3><?= htmlspecialchars($SETTINGS['booking']['success_title'] ?? 'Дякую! 🌸') ?></h3>
                    <p><?= htmlspecialchars($SETTINGS['booking']['success_text'] ?? 'Ваша заявка надіслана. Я зв\'яжуся з вами найближчим часом для підтвердження дати та деталей зйомки.') ?></p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
