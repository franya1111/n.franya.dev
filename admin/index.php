<?php
$page_title = 'Дашборд';
require_once __DIR__ . '/includes/header.php';

// Статистика
$total_bookings = count($BOOKINGS);
$new_bookings = count(array_filter($BOOKINGS, fn($b) => ($b['status'] ?? 'new') === 'new'));
$total_reviews = count($REVIEWS);
$total_categories = count($CATEGORIES);
$total_packages = 0;
foreach ($CATEGORIES as $cat) {
    $total_packages += count($cat['packages'] ?? []);
}
$total_faqs = count($FAQS);

// Останні бронювання
$recent_bookings = array_slice(array_reverse($BOOKINGS), 0, 5);
?>

<?php if (isset($_GET['welcome'])): ?>
    <div class="alert alert-success">
        ✅ Ласкаво просимо! Ваш акаунт адміністратора успішно створено.
    </div>
<?php endif; ?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 16H5V9h14v11z"/></svg>
        </div>
        <div class="stat-card-label">Всього бронювань</div>
        <div class="stat-card-value"><?= $total_bookings ?></div>
        <?php if ($new_bookings > 0): ?>
            <div style="margin-top:8px;font-size:12px;color:var(--gold,#c9a96e);"><?= $new_bookings ?> нових</div>
        <?php endif; ?>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="stat-card-label">Відгуків</div>
        <div class="stat-card-value"><?= $total_reviews ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7h-9M20 17h-9M14 7V3M14 21v-4M4 7h.01M4 17h.01"/><path d="M5 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM5 13a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
        </div>
        <div class="stat-card-label">Категорій / пакетів</div>
        <div class="stat-card-value"><?= $total_categories ?> <span style="font-size:18px;color:#9a9590;">/ <?= $total_packages ?></span></div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="stat-card-label">FAQ</div>
        <div class="stat-card-value"><?= $total_faqs ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Останні бронювання</h2>
        <a href="bookings.php" class="btn btn-ghost btn-sm">Всі бронювання →</a>
    </div>

    <?php if (empty($recent_bookings)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;margin:0 auto 16px;display:block;opacity:0.5;"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 16H5V9h14v11z"/></svg>
            <p>Поки що немає бронювань. Коли клієнт заповнить форму на сайті — вона з'явиться тут.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Дата заявки</th>
                        <th>Ім'я</th>
                        <th>Телефон</th>
                        <th>Категорія</th>
                        <th>Бажана дата</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $b): ?>
                        <tr>
                            <td><?= date('d.m.Y H:i', strtotime($b['created_at'])) ?></td>
                            <td><?= htmlspecialchars($b['name']) ?></td>
                            <td><?= htmlspecialchars($b['phone']) ?></td>
                            <td><?= htmlspecialchars($b['category'] ?? '—') ?></td>
                            <td><?= !empty($b['date']) ? date('d.m.Y', strtotime($b['date'])) : '—' ?></td>
                            <td><span class="badge badge-<?= ($b['status'] ?? 'new') === 'new' ? 'new' : 'read' ?>"><?= ($b['status'] ?? 'new') === 'new' ? 'Нова' : 'Прочитана' ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2 class="card-title mb-4">Швидкі дії</h2>
    <div class="flex gap-3" style="flex-wrap:wrap;">
        <a href="categories.php" class="btn btn-ghost btn-sm">✏️ Редагувати категорії та ціни</a>
        <a href="reviews.php" class="btn btn-ghost btn-sm">⭐ Додати відгук</a>
        <a href="faqs.php" class="btn btn-ghost btn-sm">❓ Редагувати FAQ</a>
        <a href="settings.php" class="btn btn-ghost btn-sm">⚙️ Налаштування (Telegram, Email)</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
