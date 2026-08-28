<?php
$page_title = 'Категорії та ціни';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_category') {
        $cat_id = $_POST['cat_id'] ?? '';
        if (!isset($CATEGORIES[$cat_id])) {
            $error = "Категорію не знайдено: $cat_id";
        } else {
            // Оновити основні поля
            $CATEGORIES[$cat_id]['title'] = trim($_POST['title'] ?? $CATEGORIES[$cat_id]['title']);
            $CATEGORIES[$cat_id]['description'] = trim($_POST['description'] ?? $CATEGORIES[$cat_id]['description']);
            $CATEGORIES[$cat_id]['image'] = trim($_POST['image'] ?? $CATEGORIES[$cat_id]['image']);

            // Long description — textarea по рядках
            $long_desc_raw = $_POST['long_desc'] ?? '';
            $long_desc = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r\n", "\n", $long_desc_raw)))));
            $CATEGORIES[$cat_id]['long_desc'] = $long_desc;

            // Gallery
            $gallery_raw = $_POST['gallery'] ?? '';
            $gallery = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r\n", "\n", $gallery_raw)))));
            $CATEGORIES[$cat_id]['gallery'] = $gallery;

            // Gift
            $gift_raw = $_POST['gift'] ?? '';
            $gift = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r\n", "\n", $gift_raw)))));
            $CATEGORIES[$cat_id]['gift'] = $gift;

            // Extras
            $extras_titles = $_POST['extra_title'] ?? [];
            $extras_descs = $_POST['extra_description'] ?? [];
            $extras = [];
            for ($i = 0; $i < count($extras_titles); $i++) {
                if (!empty(trim($extras_titles[$i]))) {
                    $extras[] = [
                        'title' => trim($extras_titles[$i]),
                        'description' => trim($extras_descs[$i] ?? ''),
                    ];
                }
            }
            $CATEGORIES[$cat_id]['extras'] = $extras;

            // Packages
            $pkg_names = $_POST['pkg_name'] ?? [];
            $pkg_durations = $_POST['pkg_duration'] ?? [];
            $pkg_prices = $_POST['pkg_price'] ?? [];
            $pkg_images = $_POST['pkg_image'] ?? [];
            $pkg_populars = $_POST['pkg_popular'] ?? [];
            $pkg_features = $_POST['pkg_features'] ?? [];

            $packages = [];
            for ($i = 0; $i < count($pkg_names); $i++) {
                if (empty(trim($pkg_names[$i]))) continue;
                $features_raw = $pkg_features[$i] ?? '';
                $features = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r\n", "\n", $features_raw)))));
                $packages[] = [
                    'name' => trim($pkg_names[$i]),
                    'duration' => trim($pkg_durations[$i] ?? ''),
                    'price' => trim($pkg_prices[$i] ?? ''),
                    'image' => trim($pkg_images[$i] ?? ''),
                    'popular' => in_array($i, $pkg_populars),
                    'features' => $features,
                ];
            }
            $CATEGORIES[$cat_id]['packages'] = $packages;

            if (save_json('categories.json', $CATEGORIES)) {
                $success = "✅ Категорію «{$CATEGORIES[$cat_id]['title']}» збережено";
            } else {
                $error = 'Помилка збереження. Перевірте права на data/categories.json';
            }
        }
        // Refresh
        $CATEGORIES = load_json('categories.json', []);
    }
}

$active_cat = $_GET['cat'] ?? (array_key_first($CATEGORIES) ?? '');
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<div class="card">
    <p class="text-sm text-muted mb-2">Оберіть категорію для редагування:</p>
    <div class="category-tabs">
        <?php foreach ($CATEGORIES as $id => $cat): ?>
            <a href="categories.php?cat=<?= urlencode($id) ?>" class="category-tab <?= $id === $active_cat ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['title']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($active_cat && isset($CATEGORIES[$active_cat])):
    $cat = $CATEGORIES[$active_cat];
?>
    <form method="post" action="categories.php?cat=<?= urlencode($active_cat) ?>">
        <input type="hidden" name="action" value="update_category">
        <input type="hidden" name="cat_id" value="<?= htmlspecialchars($active_cat) ?>">

        <div class="card">
            <h2 class="card-title mb-4">Основне</h2>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Назва</label>
                    <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($cat['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Головне фото (шлях)</label>
                    <input type="text" name="image" class="form-input" value="<?= htmlspecialchars($cat['image']) ?>" placeholder="img/service-individual.jpg">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Короткий опис (під заголовком на сторінці категорії)</label>
                <textarea name="description" rows="2" class="form-textarea"><?= htmlspecialchars($cat['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Детальний опис (один абзац на рядок)</label>
                <textarea name="long_desc" rows="6" class="form-textarea" placeholder="Кожен абзац з нового рядка"><?= htmlspecialchars(implode("\n", $cat['long_desc'] ?? [])) ?></textarea>
                <p class="form-help">Кожен рядок = окремий абзац тексту на сторінці.</p>
            </div>
            <div class="form-group">
                <label class="form-label">Галерея (по одному шляху на рядок, 3 фото)</label>
                <textarea name="gallery" rows="3" class="form-textarea"><?= htmlspecialchars(implode("\n", $cat['gallery'] ?? [])) ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Подарунки (по одному на рядок)</label>
                <textarea name="gift" rows="3" class="form-textarea"><?= htmlspecialchars(implode("\n", $cat['gift'] ?? [])) ?></textarea>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title mb-4">Цінові пакети</h2>
            <?php foreach ($cat['packages'] ?? [] as $i => $pkg): ?>
                <div class="package-card">
                    <div class="package-card-header">
                        <div class="package-card-title">Пакет #<?= $i + 1 ?></div>
                        <div class="checkbox-group">
                            <input type="checkbox" name="pkg_popular[]" value="<?= $i ?>" id="popular_<?= $i ?>" <?= !empty($pkg['popular']) ? 'checked' : '' ?>>
                            <label for="popular_<?= $i ?>">Популярний</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Назва</label>
                            <input type="text" name="pkg_name[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($pkg['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Тривалість</label>
                            <input type="text" name="pkg_duration[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($pkg['duration']) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ціна</label>
                            <input type="text" name="pkg_price[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($pkg['price']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Фото (шлях)</label>
                            <input type="text" name="pkg_image[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($pkg['image']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Що входить (по одному на рядок)</label>
                        <textarea name="pkg_features[<?= $i ?>]" rows="6" class="form-textarea"><?= htmlspecialchars(implode("\n", $pkg['features'] ?? [])) ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
            <p class="form-help">💡 Кількість пакетів фіксована (по 3 на категорію). Щоб змінити — відредагуйте прямо тут.</p>
        </div>

        <div class="card">
            <h2 class="card-title mb-4">Додаткові послуги</h2>
            <?php foreach ($cat['extras'] ?? [] as $i => $ex): ?>
                <div class="form-group" style="background:rgba(255,255,255,0.02);padding:12px;border-radius:8px;">
                    <label class="form-label">Назва додаткової послуги #<?= $i + 1 ?></label>
                    <input type="text" name="extra_title[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($ex['title']) ?>">
                    <label class="form-label" style="margin-top:8px;">Опис</label>
                    <textarea name="extra_description[<?= $i ?>]" rows="2" class="form-textarea"><?= htmlspecialchars($ex['description']) ?></textarea>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="position:sticky;bottom:0;background:rgba(10,10,10,0.95);backdrop-filter:blur(10px);padding:16px 0;margin-top:24px;border-top:1px solid rgba(255,255,255,0.08);">
            <button type="submit" class="btn btn-primary">💾 Зберегти категорію «<?= htmlspecialchars($cat['title']) ?>»</button>
        </div>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
