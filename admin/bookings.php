<?php
$page_title = 'Бронювання';
require_once __DIR__ . '/includes/header.php';

// Оновити статус бронювання
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? '';

    if ($action === 'mark_read') {
        foreach ($BOOKINGS as &$b) {
            if ($b['id'] === $id) { $b['status'] = 'read'; break; }
        }
        save_json('bookings.json', $BOOKINGS);
        echo '<div class="alert alert-success">✅ Заявку позначено як прочитану</div>';
    } elseif ($action === 'mark_new') {
        foreach ($BOOKINGS as &$b) {
            if ($b['id'] === $id) { $b['status'] = 'new'; break; }
        }
        save_json('bookings.json', $BOOKINGS);
        echo '<div class="alert alert-info">📌 Заявку повернуто до нових</div>';
    } elseif ($action === 'delete') {
        $BOOKINGS = array_values(array_filter($BOOKINGS, fn($b) => $b['id'] !== $id));
        save_json('bookings.json', $BOOKINGS);
        echo '<div class="alert alert-success">🗑 Заявку видалено</div>';
    }
    // Refresh
    $BOOKINGS = load_json('bookings.json', []);
}

// Сортуємо: спочатку нові, потім за датою заявки (найновіші зверху)
usort($BOOKINGS, function($a, $b) {
    $a_new = ($a['status'] ?? 'new') === 'new';
    $b_new = ($b['status'] ?? 'new') === 'new';
    if ($a_new !== $b_new) return $a_new ? -1 : 1;
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$new_count = count(array_filter($BOOKINGS, fn($b) => ($b['status'] ?? 'new') === 'new'));
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Бронювання (<?= count($BOOKINGS) ?>)</h2>
        <?php if ($new_count > 0): ?>
            <span class="badge badge-new"><?= $new_count ?> нових</span>
        <?php endif; ?>
    </div>

    <?php if (empty($BOOKINGS)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;margin:0 auto 16px;display:block;opacity:0.5;"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 16H5V9h14v11z"/></svg>
            <p>Поки що немає бронювань.</p>
            <p class="text-sm text-muted mt-2">Коли клієнт заповнить форму на сайті — заявка з'явиться тут автоматично.</p>
        </div>
    <?php else: ?>
        <?php foreach ($BOOKINGS as $b): ?>
            <div class="booking-item" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px;margin-bottom:16px;<?= ($b['status'] ?? 'new') === 'new' ? 'border-left:3px solid var(--gold,#c9a96e);' : '' ?>">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <h3 style="font-family:'Forum',serif;font-size:20px;margin:0 0 4px;"><?= htmlspecialchars($b['name']) ?></h3>
                        <p class="text-xs text-muted" style="margin:0;">
                            <?= date('d.m.Y H:i', strtotime($b['created_at'])) ?>
                            <?php if (($b['status'] ?? 'new') === 'new'): ?>
                                <span class="badge badge-new" style="margin-left:8px;">Нова</span>
                            <?php else: ?>
                                <span class="badge badge-read" style="margin-left:8px;">Прочитана</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex gap-2" style="flex-wrap:wrap;">
                        <?php if (($b['status'] ?? 'new') === 'new'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($b['id']) ?>">
                                <button type="submit" class="btn btn-ghost btn-sm">✓ Прочитано</button>
                            </form>
                        <?php else: ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="mark_new">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($b['id']) ?>">
                                <button type="submit" class="btn btn-ghost btn-sm">↑ Повернути до нових</button>
                            </form>
                        <?php endif; ?>
                        <a href="https://t.me/<?= preg_replace('/[^a-zA-Z0-9_]/', '', $b['phone']) ?>" target="_blank" class="btn btn-ghost btn-sm">Telegram</a>
                        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $b['phone']) ?>" class="btn btn-ghost btn-sm">Подзвонити</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($b['id']) ?>">
                            <button type="submit" class="btn btn-danger" data-confirm="Видалити заявку від <?= htmlspecialchars($b['name']) ?>?">🗑</button>
                        </form>
                    </div>
                </div>

                <div class="booking-detail">
                    <div>
                        <div class="booking-detail-label">Телефон</div>
                        <div class="booking-detail-value">
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $b['phone']) ?>" style="color:var(--gold,#c9a96e);text-decoration:none;"><?= htmlspecialchars($b['phone']) ?></a>
                        </div>
                    </div>
                    <div>
                        <div class="booking-detail-label">Категорія зйомки</div>
                        <div class="booking-detail-value"><?= htmlspecialchars($b['category'] ?: '—') ?></div>
                    </div>
                    <div>
                        <div class="booking-detail-label">Бажана дата</div>
                        <div class="booking-detail-value"><?= !empty($b['date']) ? date('d.m.Y', strtotime($b['date'])) : '—' ?></div>
                    </div>
                    <div>
                        <div class="booking-detail-label">IP</div>
                        <div class="booking-detail-value text-sm text-muted"><?= htmlspecialchars($b['ip'] ?? '—') ?></div>
                    </div>
                </div>

                <?php if (!empty($b['message'])): ?>
                    <div style="margin-top:12px;">
                        <div class="booking-detail-label">Повідомлення клієнта</div>
                        <div class="booking-detail-value" style="padding:12px;background:rgba(255,255,255,0.03);border-radius:8px;border-left:3px solid var(--gold,#c9a96e);"><?= nl2br(htmlspecialchars($b['message'])) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
