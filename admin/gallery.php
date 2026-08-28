<?php
$page_title = 'Галерея прикладів';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $image = trim($_POST['image'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');

        if (empty($image)) {
            $error = 'Вкажіть шлях до фото або завантажте нове';
        } else {
            $item = [
                'id' => 'g' . uniqid(),
                'image' => $image,
                'title' => mb_substr($title, 0, 100),
                'category' => $category,
            ];
            $GALLERY[] = $item;
            if (save_json('gallery.json', $GALLERY)) {
                $success = '✅ Фото додано в галерею';
            } else {
                $error = 'Помилка збереження. Перевірте права на data/gallery.json';
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $image_to_delete = '';
        foreach ($GALLERY as $i => $g) {
            if ($g['id'] === $id) {
                $image_to_delete = $g['image'];
                array_splice($GALLERY, $i, 1);
                break;
            }
        }
        $GALLERY = array_values($GALLERY);
        if (save_json('gallery.json', $GALLERY)) {
            // Видаляємо файл якщо він в uploads/
            if (!empty($image_to_delete) && strpos($image_to_delete, 'uploads/') === 0) {
                $real_path = PROJECT_ROOT . '/' . $image_to_delete;
                if (file_exists($real_path)) @unlink($real_path);
            }
            $success = '🗑 Фото видалено з галереї';
        }
    }
    // Refresh
    $GALLERY = load_json('gallery.json', []);
}

$category_options = [
    'individual' => 'Індивідуальна',
    'wedding' => 'Весільна',
    'love-story' => 'Love Story',
    'birthday' => 'День народження',
    'pregnancy' => 'Вагітність',
    'family' => 'Сімейна',
];
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<div class="card">
    <h2 class="card-title mb-4">Додати фото в галерею</h2>
    <form method="post" id="addGalleryForm">
        <input type="hidden" name="action" value="add">
        <div class="image-upload-block" data-target-input="new_image">
            <div class="image-preview-wrapper">
                <span class="image-preview-placeholder">📷 Завантажте фото</span>
            </div>
            <div class="image-actions">
                <label class="btn btn-ghost btn-sm">
                    📤 Завантажити фото
                    <input type="file" name="upload_gallery_new" accept="image/*" class="file-input" data-target-field="new_image">
                </label>
                <span class="upload-status"></span>
            </div>
        </div>
        <input type="hidden" name="image" id="new_image" value="">
        <div class="form-row mt-4">
            <div class="form-group">
                <label class="form-label">Заголовок (опц.)</label>
                <input type="text" name="title" class="form-input" placeholder="Напр.: Весільна зйомка">
            </div>
            <div class="form-group">
                <label class="form-label">Категорія</label>
                <select name="category" class="form-select">
                    <option value="">— без категорії —</option>
                    <?php foreach ($category_options as $id => $name): ?>
                        <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">+ Додати в галерею</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Всі фото (<?= count($GALLERY) ?>)</h2>
    </div>

    <?php if (empty($GALLERY)): ?>
        <div class="empty-state">
            <p>Галерея порожня. Додайте перше фото вище.</p>
        </div>
    <?php else: ?>
        <div class="gallery-admin-grid">
            <?php foreach ($GALLERY as $g): ?>
                <div class="gallery-admin-item">
                    <img src="<?= htmlspecialchars($g['image']) ?>" alt="<?= htmlspecialchars($g['title']) ?>" loading="lazy" onerror="this.style.opacity='0.3';this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23222%22 width=%22100%22 height=%22100%22/><text fill=%22%23999%22 x=%2250%22 y=%2255%22 font-size=%2210%22 text-anchor=%22middle%22>No image</text></svg>';">
                    <div class="gallery-admin-info">
                        <div class="gallery-admin-title"><?= htmlspecialchars($g['title'] ?: '—') ?></div>
                        <div class="gallery-admin-cat">
                            <?php if (!empty($g['category']) && isset($category_options[$g['category']])): ?>
                                <span class="badge badge-popular"><?= htmlspecialchars($category_options[$g['category']]) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="gallery-admin-path text-xs text-muted"><?= htmlspecialchars($g['image']) ?></div>
                    </div>
                    <form method="post" onsubmit="return confirm('Видалити це фото з галереї?');" style="margin-top:8px;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($g['id']) ?>">
                        <button type="submit" class="btn btn-danger w-full">🗑 Видалити</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
