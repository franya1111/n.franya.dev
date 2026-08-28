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

            // Extras — масив з довільною кількістю
            $extras_titles = $_POST['extra_title'] ?? [];
            $extras_descs = $_POST['extra_description'] ?? [];
            $extras = [];
            for ($i = 0; $i < count($extras_titles); $i++) {
                $title = trim($extras_titles[$i] ?? '');
                if (!empty($title)) {
                    $extras[] = [
                        'title' => mb_substr($title, 0, 200),
                        'description' => mb_substr(trim($extras_descs[$i] ?? ''), 0, 2000),
                    ];
                }
            }
            $CATEGORIES[$cat_id]['extras'] = $extras;

            // Цінові варіанти — масив з довільною кількістю
            $pkg_names = $_POST['pkg_name'] ?? [];
            $pkg_durations = $_POST['pkg_duration'] ?? [];
            $pkg_prices = $_POST['pkg_price'] ?? [];
            $pkg_images = $_POST['pkg_image'] ?? [];
            $pkg_populars = $_POST['pkg_popular'] ?? [];
            $pkg_features = $_POST['pkg_features'] ?? [];

            $packages = [];
            for ($i = 0; $i < count($pkg_names); $i++) {
                $name = trim($pkg_names[$i] ?? '');
                if (empty($name)) continue;
                $features_raw = $pkg_features[$i] ?? '';
                $features = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r\n", "\n", $features_raw)))));
                $packages[] = [
                    'name' => mb_substr($name, 0, 100),
                    'duration' => mb_substr(trim($pkg_durations[$i] ?? ''), 0, 100),
                    'price' => mb_substr(trim($pkg_prices[$i] ?? ''), 0, 100),
                    'image' => mb_substr(trim($pkg_images[$i] ?? ''), 0, 255),
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
    <form method="post" action="categories.php?cat=<?= urlencode($active_cat) ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_category">
        <input type="hidden" name="cat_id" value="<?= htmlspecialchars($active_cat) ?>">

        <!-- ============ ОСНОВНЕ ============ -->
        <div class="card">
            <h2 class="card-title mb-4">📷 Основне</h2>

            <!-- Головне фото -->
            <div class="image-upload-block" data-target-input="image">
                <div class="image-preview-wrapper">
                    <?php if (!empty($cat['image'])): ?>
                        <img src="<?= htmlspecialchars($cat['image']) ?>" alt="Прев'ю" class="image-preview" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <span class="image-preview-placeholder" style="display:none;">⚠ Не знайдено<br><?= htmlspecialchars($cat['image']) ?></span>
                    <?php else: ?>
                        <span class="image-preview-placeholder">📷 Немає фото</span>
                    <?php endif; ?>
                </div>
                <div class="image-actions">
                    <label class="form-label">Головне фото</label>
                    <input type="text" name="image" class="form-input" value="<?= htmlspecialchars($cat['image']) ?>" placeholder="img/service-individual.jpg або uploads/photo.jpg">
                    <p class="form-help">Шлях від кореня сайту. Можна ввести вручну або завантажити нове фото кнопкою нижче.</p>
                    <label class="btn btn-ghost btn-sm">
                        📤 Завантажити нове
                        <input type="file" name="upload_image" accept="image/*" class="file-input" data-old-path="<?= htmlspecialchars($cat['image']) ?>" data-target-field="image">
                    </label>
                    <span class="upload-status"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Назва категорії *</label>
                    <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($cat['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Короткий опис (під заголовком)</label>
                    <input type="text" name="description" class="form-input" value="<?= htmlspecialchars($cat['description']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Детальний опис (один абзац на рядок)</label>
                <textarea name="long_desc" rows="6" class="form-textarea" placeholder="Кожен абзац з нового рядка"><?= htmlspecialchars(implode("\n", $cat['long_desc'] ?? [])) ?></textarea>
                <p class="form-help">Кожен рядок = окремий абзац тексту на сторінці.</p>
            </div>

            <!-- Галерея -->
            <div class="form-group">
                <label class="form-label">🖼 Галерея на сторінці категорії</label>
                <textarea name="gallery" rows="3" class="form-textarea" id="gallery-input" placeholder="По одному шляху на рядок"><?= htmlspecialchars(implode("\n", $cat['gallery'] ?? [])) ?></textarea>
                <p class="form-help">Можна завантажити нові фото — вони автоматично додадуться сюди.</p>
                <div class="image-upload-block" data-target-input="gallery">
                    <label class="btn btn-ghost btn-sm mt-2">
                        📤 Завантажити фото для галереї (можна кілька)
                        <input type="file" name="upload_gallery[]" accept="image/*" multiple class="file-input" data-append-to="gallery-input">
                    </label>
                    <span class="upload-status"></span>
                </div>
                <?php if (!empty($cat['gallery'])): ?>
                    <div class="gallery-preview-grid">
                        <?php foreach ($cat['gallery'] as $g_img): ?>
                            <div class="gallery-preview-item">
                                <img src="<?= htmlspecialchars($g_img) ?>" alt="<?= htmlspecialchars($g_img) ?>" onerror="this.style.opacity='0.3';">
                                <span class="gallery-preview-path"><?= htmlspecialchars($g_img) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Подарунки -->
            <div class="form-group">
                <label class="form-label">🎁 Подарунки (по одному на рядок)</label>
                <textarea name="gift" rows="3" class="form-textarea"><?= htmlspecialchars(implode("\n", $cat['gift'] ?? [])) ?></textarea>
                <p class="form-help">Наприклад: «Постійні клієнти — 10%», «Військові — 10%»</p>
            </div>
        </div>

        <!-- ============ ЦІНОВІ ВАРІАНТИ ============ -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">💰 Цінові варіанти</h2>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addPackage()">+ Додати варіант</button>
            </div>
            <p class="form-help mb-4">Кожна категорія має власні ціни. Кількість варіантів — будь-яка (рекомендується 2-4).</p>

            <div id="packages-container">
                <?php foreach ($cat['packages'] ?? [] as $i => $pkg): ?>
                    <div class="package-card" data-package-index="<?= $i ?>">
                        <div class="package-card-header">
                            <div class="package-card-title"> Варіант #<?= $i + 1 ?></div>
                            <div class="flex items-center gap-3">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="pkg_popular[]" value="<?= $i ?>" id="popular_<?= $i ?>" <?= !empty($pkg['popular']) ? 'checked' : '' ?>>
                                    <label for="popular_<?= $i ?>">⭐ Популярний</label>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removePackage(this)">🗑 Видалити</button>
                            </div>
                        </div>

                        <!-- Фото варіанту -->
                        <div class="image-upload-block" data-target-input="pkg_image_<?= $i ?>">
                            <div class="image-preview-wrapper">
                                <?php if (!empty($pkg['image'])): ?>
                                    <img src="<?= htmlspecialchars($pkg['image']) ?>" alt="Прев'ю" class="image-preview" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <span class="image-preview-placeholder" style="display:none;">⚠ Не знайдено<br><?= htmlspecialchars($pkg['image']) ?></span>
                                <?php else: ?>
                                    <span class="image-preview-placeholder">📷 Немає фото</span>
                                <?php endif; ?>
                            </div>
                            <div class="image-actions">
                                <label class="form-label">Фото варіанту</label>
                                <input type="text" name="pkg_image[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($pkg['image']) ?>" placeholder="img/indiv-stand.jpg або uploads/photo.jpg">
                                <label class="btn btn-ghost btn-sm">
                                    📤 Завантажити нове
                                    <input type="file" name="upload_pkg_<?= $i ?>" accept="image/*" class="file-input" data-old-path="<?= htmlspecialchars($pkg['image']) ?>" data-target-field="pkg_image_<?= $i ?>">
                                </label>
                                <span class="upload-status"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Назва *</label>
                                <input type="text" name="pkg_name[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($pkg['name']) ?>" required placeholder="Стандартний">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Тривалість</label>
                                <input type="text" name="pkg_duration[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($pkg['duration']) ?>" placeholder="2 години">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ціна</label>
                            <input type="text" name="pkg_price[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($pkg['price']) ?>" placeholder="4500 грн">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Що входить (по одному на рядок)</label>
                            <textarea name="pkg_features[<?= $i ?>]" rows="6" class="form-textarea" placeholder="Консультація та підготовка до зйомки&#10;Комфортна зйомка з допомогою у позуванні&#10;..."><?= htmlspecialchars(implode("\n", $pkg['features'] ?? [])) ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ============ ДОДАТКОВІ ПОСЛУГИ ============ -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📋 Додаткові послуги</h2>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addExtra()">+ Додати послугу</button>
            </div>
            <p class="form-help mb-4">Опціональні послуги: фотодрук, флешки, фотокниги тощо.</p>

            <div id="extras-container">
                <?php foreach ($cat['extras'] ?? [] as $i => $ex): ?>
                    <div class="extra-card" data-extra-index="<?= $i ?>">
                        <div class="extra-card-header">
                            <span class="extra-card-title">Послуга #<?= $i + 1 ?></span>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeExtra(this)">🗑 Видалити</button>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Назва</label>
                            <input type="text" name="extra_title[<?= $i ?>]" class="form-input" value="<?= htmlspecialchars($ex['title']) ?>" placeholder="Фотодрук 10x15">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Опис</label>
                            <textarea name="extra_description[<?= $i ?>]" rows="2" class="form-textarea" placeholder="Друк ваших найкращих фотографій у форматі 10x15 см..."><?= htmlspecialchars($ex['description']) ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sticky save bar -->
        <div style="position:sticky;bottom:0;background:rgba(10,10,10,0.95);backdrop-filter:blur(10px);padding:16px 0;margin-top:24px;border-top:1px solid rgba(255,255,255,0.08);z-index:50;">
            <button type="submit" class="btn btn-primary">💾 Зберегти категорію «<?= htmlspecialchars($cat['title']) ?>»</button>
        </div>
    </form>

    <script>
    // ===== Динамічне додавання/видалення цінових варіантів =====
    let packageCounter = <?= count($cat['packages'] ?? []) ?>;

    function addPackage() {
        const container = document.getElementById('packages-container');
        const i = packageCounter;
        packageCounter++;

        const html = `
        <div class="package-card" data-package-index="${i}">
            <div class="package-card-header">
                <div class="package-card-title"> Варіант #${i + 1}</div>
                <div class="flex items-center gap-3">
                    <div class="checkbox-group">
                        <input type="checkbox" name="pkg_popular[]" value="${i}" id="popular_${i}">
                        <label for="popular_${i}">⭐ Популярний</label>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removePackage(this)">🗑 Видалити</button>
                </div>
            </div>

            <div class="image-upload-block" data-target-input="pkg_image_${i}">
                <div class="image-preview-wrapper">
                    <span class="image-preview-placeholder">📷 Немає фото</span>
                </div>
                <div class="image-actions">
                    <label class="form-label">Фото варіанту</label>
                    <input type="text" name="pkg_image[${i}]" class="form-input" value="" placeholder="img/... або uploads/photo.jpg">
                    <label class="btn btn-ghost btn-sm">
                        📤 Завантажити нове
                        <input type="file" name="upload_pkg_${i}" accept="image/*" class="file-input" data-old-path="" data-target-field="pkg_image_${i}">
                    </label>
                    <span class="upload-status"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Назва *</label>
                    <input type="text" name="pkg_name[${i}]" class="form-input" value="" required placeholder="Стандартний">
                </div>
                <div class="form-group">
                    <label class="form-label">Тривалість</label>
                    <input type="text" name="pkg_duration[${i}]" class="form-input" value="" placeholder="2 години">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Ціна</label>
                <input type="text" name="pkg_price[${i}]" class="form-input" value="" placeholder="4500 грн">
            </div>
            <div class="form-group">
                <label class="form-label">Що входить (по одному на рядок)</label>
                <textarea name="pkg_features[${i}]" rows="6" class="form-textarea" placeholder="Консультація та підготовка до зйомки"></textarea>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        // Re-bind file inputs для нових елементів
        bindFileInputs(container.lastElement.querySelector('.file-input'));
    }

    function removePackage(btn) {
        if (!confirm('Видалити цей ціновий варіант?')) return;
        const card = btn.closest('.package-card');
        card.remove();
    }

    // ===== Динамічне додавання/видалення додаткових послуг =====
    let extraCounter = <?= count($cat['extras'] ?? []) ?>;

    function addExtra() {
        const container = document.getElementById('extras-container');
        const i = extraCounter;
        extraCounter++;

        const html = `
        <div class="extra-card" data-extra-index="${i}">
            <div class="extra-card-header">
                <span class="extra-card-title">Послуга #${i + 1}</span>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeExtra(this)">🗑 Видалити</button>
            </div>
            <div class="form-group">
                <label class="form-label">Назва</label>
                <input type="text" name="extra_title[${i}]" class="form-input" value="" placeholder="Фотодрук 10x15">
            </div>
            <div class="form-group">
                <label class="form-label">Опис</label>
                <textarea name="extra_description[${i}]" rows="2" class="form-textarea" placeholder="Опис послуги..."></textarea>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
    }

    function removeExtra(btn) {
        if (!confirm('Видалити цю послугу?')) return;
        const card = btn.closest('.extra-card');
        card.remove();
    }

    // Біндити новостворені file inputs
    function bindFileInputs(input) {
        if (!input) return;
        // Викликаємо існуючий обробник з admin.js, який слухає всі .file-input
        // (event listener вже налаштований на document level через делегування)
    }
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
