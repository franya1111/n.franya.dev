<?php
/**
 * Завантаження всіх даних з JSON-файлів у data/.
 * Якщо файла немає — повертає дефолтні порожні значення.
 */

// Корінь проекту: 1 рівень вгору від includes/data.php
// includes/data.php → includes/ → project_root/
define('PROJECT_ROOT', dirname(__DIR__));
define('DATA_DIR', PROJECT_ROOT . '/data');

// Якщо папки data/ немає — спробуємо створити
if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0777, true);
}

function load_json($name, $default) {
    $file = DATA_DIR . '/' . $name;
    if (!file_exists($file)) return $default;
    $content = @file_get_contents($file);
    if ($content === false) return $default;
    $data = json_decode($content, true);
    return $data === null ? $default : $data;
}

function save_json($name, $data) {
    $file = DATA_DIR . '/' . $name;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return @file_put_contents($file, $json) !== false;
}

// === Категорії ===
$CATEGORIES = load_json('categories.json', []);

// === Відгуки ===
$REVIEWS = load_json('reviews.json', []);

// === FAQ ===
$FAQS = load_json('faqs.json', []);

// === Налаштування ===
$SETTINGS = load_json('settings.json', [
    'brand_name' => 'krasnobaeva',
    'brand_tagline' => 'photo & video',
    'instagram_url' => 'https://www.instagram.com/krasnobaeva.ph/',
    'telegram_url' => 'https://t.me/krasnobaevaph',
    'whatsapp_url' => 'https://wa.me/380938383871',
    'phone' => '+380938383871',
    'email' => 'krasnobaeva.ph@gmail.com',
    'about_text' => [],
    'about_insta_handle' => '@krasnobaeva.ph',
    'telegram_bot_token' => '',
    'telegram_chat_id' => '',
    'notifications_enabled' => true,
]);

// === Бронювання ===
$BOOKINGS = load_json('bookings.json', []);

// Допоміжна функція: отримати категорію за id
function get_category($id) {
    global $CATEGORIES;
    return $CATEGORIES[$id] ?? null;
}
