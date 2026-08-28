<?php
$page_title = 'FAQ — Питання та відповіді';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $q = trim($_POST['q'] ?? '');
        $a = trim($_POST['a'] ?? '');
        if (empty($q) || empty($a)) {
            $error = 'Заповніть питання та відповідь';
        } else {
            $FAQS[] = [
                'q' => mb_substr($q, 0, 300),
                'a' => mb_substr($a, 0, 2000),
            ];
            save_json('faqs.json', $FAQS);
            $success = '✅ Питання додано';
        }
    } elseif ($action === 'update') {
        $index = (int)($_POST['index'] ?? -1);
        $q = trim($_POST['q'] ?? '');
        $a = trim($_POST['a'] ?? '');
        if (isset($FAQS[$index]) && !empty($q) && !empty($a)) {
            $FAQS[$index] = ['q' => mb_substr($q, 0, 300), 'a' => mb_substr($a, 0, 2000)];
            save_json('faqs.json', $FAQS);
            $success = '✅ Питання оновлено';
        }
    } elseif ($action === 'delete') {
        $index = (int)($_POST['index'] ?? -1);
        if (isset($FAQS[$index])) {
            array_splice($FAQS, $index, 1);
            $FAQS = array_values($FAQS);
            save_json('faqs.json', $FAQS);
            $success = '🗑 Питання видалено';
        }
    }
    // Refresh
    $FAQS = load_json('faqs.json', []);
}

$edit_index = isset($_GET['edit']) ? (int)$_GET['edit'] : -1;
$editing = ($edit_index >= 0 && isset($FAQS[$edit_index])) ? $FAQS[$edit_index] : null;
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<div class="card">
    <h2 class="card-title mb-4"><?= $editing ? 'Редагувати питання' : 'Додати питання' ?></h2>
    <form method="post" action="faqs.php">
        <?php if ($editing): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="index" value="<?= $edit_index ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="add">
        <?php endif; ?>
        <div class="form-group">
            <label class="form-label">Питання *</label>
            <input type="text" name="q" class="form-input" required value="<?= $editing ? htmlspecialchars($editing['q']) : '' ?>" placeholder="Напр.: Чи потрібна передоплата?">
        </div>
        <div class="form-group">
            <label class="form-label">Відповідь *</label>
            <textarea name="a" rows="3" class="form-textarea" required placeholder="Текст відповіді…"><?= $editing ? htmlspecialchars($editing['a']) : '' ?></textarea>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary"><?= $editing ? 'Зберегти' : '+ Додати' ?></button>
            <?php if ($editing): ?>
                <a href="faqs.php" class="btn btn-ghost">Скасувати</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Всі питання (<?= count($FAQS) ?>)</h2>
    </div>

    <?php if (empty($FAQS)): ?>
        <div class="empty-state">
            <p>FAQ порожній. Додайте перше питання вище.</p>
        </div>
    <?php else: ?>
        <?php foreach ($FAQS as $i => $f): ?>
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:16px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:200px;">
                        <div class="font-serif" style="font-size:16px;margin-bottom:6px;color:var(--gold,#c9a96e);"><?= htmlspecialchars($f['q']) ?></div>
                        <div style="font-size:14px;line-height:1.6;color:#9a9590;"><?= nl2br(htmlspecialchars($f['a'])) ?></div>
                    </div>
                    <div class="flex gap-2">
                        <a href="faqs.php?edit=<?= $i ?>" class="btn btn-ghost btn-sm">✏️ Редагувати</a>
                        <form method="post" onsubmit="return confirm('Видалити це питання?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="index" value="<?= $i ?>">
                            <button type="submit" class="btn btn-danger">🗑</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
