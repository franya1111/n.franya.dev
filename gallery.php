<?php
require_once __DIR__ . '/includes/data.php';

// Фільтр за категорією
$filter = $_GET['cat'] ?? '';
$category_titles = [
    'individual' => 'Індивідуальна',
    'wedding' => 'Весільна',
    'love-story' => 'Love Story',
    'birthday' => 'День народження',
    'pregnancy' => 'Вагітність',
    'family' => 'Сімейна',
];

// Фільтруємо
$filtered = [];
foreach ($GALLERY as $g) {
    if (empty($filter) || $g['category'] === $filter) {
        $filtered[] = $g;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<main>
    <section class="category-hero" style="min-height: 50vh;">
        <div class="category-hero-bg">
            <img src="img/hero.jpg" alt="Галерея">
        </div>
        <div class="category-hero-content">
            <span class="category-hero-label">Портфоліо</span>
            <h1 class="category-hero-title">Галерея</h1>
            <p class="category-hero-desc">Приклади моїх робіт — оберіть категорію, щоб відфільтрувати</p>
        </div>
    </section>

    <section class="pricing" style="background: transparent;">
        <div class="container">
            <!-- Фільтри -->
            <div class="category-tabs" style="justify-content: center;">
                <a href="gallery.php" class="category-tab <?= empty($filter) ? 'active' : '' ?>">Всі</a>
                <?php foreach ($category_titles as $id => $name): ?>
                    <a href="gallery.php?cat=<?= urlencode($id) ?>" class="category-tab <?= $filter === $id ? 'active' : '' ?>">
                        <?= htmlspecialchars($name) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($filtered)): ?>
                <div class="empty-state">
                    <p>Поки що немає фото в цій категорії.</p>
                    <p class="text-sm text-muted mt-2">Додайте фото через адмінку — воно автоматично з'явиться тут.</p>
                </div>
            <?php else: ?>
                <div class="public-gallery-grid">
                    <?php foreach ($filtered as $g): ?>
                        <div class="public-gallery-item" data-category="<?= htmlspecialchars($g['category']) ?>">
                            <img src="<?= htmlspecialchars(asset_url($g['image'])) ?>" alt="<?= htmlspecialchars($g['title']) ?>" loading="lazy">
                            <div class="public-gallery-overlay">
                                <span class="public-gallery-title"><?= htmlspecialchars($g['title']) ?></span>
                                <?php if (!empty($g['category']) && isset($category_titles[$g['category']])): ?>
                                    <span class="public-gallery-cat"><?= htmlspecialchars($category_titles[$g['category']]) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div style="max-width: 640px; margin: 0 auto; padding: 0 24px;">
            <h2 class="cta-title">Сподобалось?</h2>
            <p class="cta-text">Заповніть коротку анкету — і я зв'яжуся з вами для підтвердження деталей.</p>
            <div class="cta-btns">
                <a href="index.php#booking" class="btn-primary">Заповнити анкету</a>
                <a href="index.php" class="btn-ghost">← Повернутись на головну</a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
