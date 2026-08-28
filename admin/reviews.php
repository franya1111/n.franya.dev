<?php
$page_title = 'Відгуки';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $text = trim($_POST['text'] ?? '');
        if (empty($name) || empty($text)) {
            $error = 'Заповніть ім\'я та текст відгуку';
        } else {
            $review = [
                'name' => mb_substr($name, 0, 50),
                'text' => mb_substr($text, 0, 500),
                'created_at' => date('c'),
            ];
            $REVIEWS[] = $review;
            if (save_json('reviews.json', $REVIEWS)) {
                $success = '✅ Відгук додано';
            } else {
                $error = 'Не вдалося зберегти. Перевірте права на data/reviews.json';
            }
        }
    } elseif ($action === 'delete') {
        $index = (int)($_POST['index'] ?? -1);
        if (isset($REVIEWS[$index])) {
            array_splice($REVIEWS, $index, 1);
            $REVIEWS = array_values($REVIEWS);
            save_json('reviews.json', $REVIEWS);
            $success = '🗑 Відгук видалено';
        }
    }
    // Refresh
    $REVIEWS = load_json('reviews.json', []);
}

// Сортуємо: найновіші зверху
usort($REVIEWS, fn($a, $b) => strtotime($b['created_at'] ?? '0') - strtotime($a['created_at'] ?? '0'));
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<div class="card">
    <h2 class="card-title mb-4">Додати відгук</h2>
    <form method="post">
        <input type="hidden" name="action" value="add">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Ім'я клієнта *</label>
                <input type="text" name="name" class="form-input" required placeholder="Напр.: Олена">
            </div>
            <div class="form-group">
                <label class="form-label">Дата (для довідки)</label>
                <input type="text" class="form-input" value="<?= date('d.m.Y') ?>" disabled>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Текст відгуку *</label>
            <textarea name="text" rows="3" class="form-textarea" required placeholder="Текст відгуку українською або російською…"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">+ Додати відгук</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Всі відгуки (<?= count($REVIEWS) ?>)</h2>
    </div>

    <?php if (empty($REVIEWS)): ?>
        <div class="empty-state">
            <p>Відгуків ще немає. Додайте перший вище.</p>
        </div>
    <?php else: ?>
        <?php foreach ($REVIEWS as $i => $r): ?>
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:16px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                    <div class="flex items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--gold,#c9a96e),var(--gold-dark,#a8873f));display:flex;align-items:center;justify-content:center;color:#0a0a0a;font-weight:bold;">
                            <?= htmlspecialchars(mb_substr($r['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="font-serif" style="font-size:18px;"><?= htmlspecialchars($r['name']) ?></div>
                            <div class="text-xs text-muted"><?= !empty($r['created_at']) ? date('d.m.Y', strtotime($r['created_at'])) : '' ?></div>
                        </div>
                    </div>
                    <form method="post" onsubmit="return confirm('Видалити відгук від <?= htmlspecialchars($r['name']) ?>?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="index" value="<?= $i ?>">
                        <button type="submit" class="btn btn-danger">🗑 Видалити</button>
                    </form>
                </div>
                <p style="margin:0;font-size:14px;line-height:1.6;color:#e8e4de;"><?= nl2br(htmlspecialchars($r['text'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
