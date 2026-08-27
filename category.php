<?php
require_once __DIR__ . '/includes/data.php';

$id = $_GET['id'] ?? '';
$category = get_category($id);

if (!$category) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>
<main>
    <!-- CATEGORY HERO -->
    <section class="category-hero">
        <div class="category-hero-bg">
            <img src="<?= htmlspecialchars($category['image']) ?>" alt="<?= htmlspecialchars($category['title']) ?>">
        </div>
        <div class="category-hero-content">
            <span class="category-hero-label">Зйомка</span>
            <h1 class="category-hero-title"><?= htmlspecialchars($category['title']) ?></h1>
            <p class="category-hero-desc"><?= htmlspecialchars($category['description']) ?></p>
            <a href="#packages" class="btn-primary category-hero-cta">Переглянути пакети</a>
        </div>
    </section>

    <!-- ABOUT THIS CATEGORY -->
    <section class="category-about">
        <div class="container" style="max-width: 900px;">
            <div class="category-about-header">
                <span class="section-label">Про зйомку</span>
                <h2 class="section-title">Як це проходить</h2>
            </div>
            <div class="category-about-text">
                <?php foreach ($category['long_desc'] as $p): ?>
                    <p><?= htmlspecialchars($p) ?></p>
                <?php endforeach; ?>
            </div>

            <div class="category-gallery">
                <?php foreach ($category['gallery'] as $i => $img): ?>
                    <div class="category-gallery-item <?= $i === 1 ? 'middle' : '' ?>">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($category['title']) ?> <?= $i + 1 ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- PRICING PACKAGES -->
    <section class="pricing" id="packages">
        <div class="container">
            <div class="pricing-header">
                <span class="section-label">Пакети</span>
                <h2 class="section-title"><?= htmlspecialchars($category['title']) ?></h2>
                <p class="section-subtitle">Оберіть свій пакет нижче — кожен можна адаптувати під ваші побажання.</p>
            </div>

            <div class="pricing-grid">
                <?php foreach ($category['packages'] as $pkg): ?>
                    <div class="pricing-card <?= !empty($pkg['popular']) ? 'popular' : '' ?> reveal">
                        <?php if (!empty($pkg['popular'])): ?>
                            <span class="popular-badge">Популярний</span>
                        <?php endif; ?>

                        <div class="pricing-img">
                            <img src="<?= htmlspecialchars($pkg['image']) ?>" alt="<?= htmlspecialchars($pkg['name']) ?>">
                            <div class="pricing-img-info">
                                <h3><?= htmlspecialchars($pkg['name']) ?></h3>
                                <span class="pricing-duration"><?= htmlspecialchars($pkg['duration']) ?></span>
                            </div>
                        </div>

                        <div class="pricing-body">
                            <div class="pricing-price"><?= htmlspecialchars($pkg['price']) ?></div>

                            <ul class="pricing-features">
                                <?php foreach ($pkg['features'] as $f): ?>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span><?= htmlspecialchars($f) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <a href="index.php#booking" class="btn-primary pricing-cta"><?= htmlspecialchars($pkg['name'] === 'Мінімальний' && $id === 'wedding' ? 'Перевірити вільну дату' : 'Забронювати дату') ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- GIFT -->
            <div class="gift reveal">
                <div class="gift-inner">
                    <div class="gift-header">
                        <div class="gift-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                        </div>
                        <div>
                            <h3 class="gift-title">Подарунок</h3>
                            <p class="gift-note">Напишіть мені — підкажу деталі та підберу ідеальний формат</p>
                        </div>
                    </div>
                    <div class="gift-items">
                        <?php foreach ($category['gift'] as $g): ?>
                            <span class="gift-item"><?= htmlspecialchars($g) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- EXTRAS -->
            <div class="extras reveal">
                <h3 class="extras-title">Додаткові послуги</h3>
                <?php foreach ($category['extras'] as $ex): ?>
                    <div class="extra-item">
                        <button class="extra-btn" type="button">
                            <span><?= htmlspecialchars($ex['title']) ?></span>
                            <span class="extra-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </span>
                        </button>
                        <div class="extra-answer">
                            <p><?= htmlspecialchars($ex['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div style="max-width: 640px; margin: 0 auto; padding: 0 24px;">
            <h2 class="cta-title">Готові забронювати?</h2>
            <p class="cta-text">Заповніть коротку анкету — і я зв'яжуся з вами для підтвердження деталей.</p>
            <div class="cta-btns">
                <a href="index.php#booking" class="btn-primary">Заповнити анкету</a>
                <a href="index.php" class="btn-ghost">← Повернутись на головну</a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
