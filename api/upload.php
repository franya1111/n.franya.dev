<?php
/**
 * API endpoint для завантаження фотографій через адмінку.
 *
 * POST (multipart/form-data):
 *   - file: зображення (jpg/jpeg/png/webp, макс 10MB)
 *   - old_path: (опц.) шлях до старого фото, щоб видалити його
 *
 * Повертає JSON:
 *   {success: true, path: "uploads/xxx.jpg"} при успіху
 *   {success: false, message: "..."} при помилці
 *
 * Авторизація: тільки адмін (через сесію)
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// Підключаємо auth + data
require_once dirname(__DIR__, 2) . '/admin/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/data.php';

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Необхідно увійти в адмінку']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не підтримується']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $err_code = $_FILES['file']['error'] ?? -1;
    $err_msg = 'Помилка завантаження файлу';
    switch ($err_code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $err_msg = 'Файл занадто великий (макс 10MB)';
            break;
        case UPLOAD_ERR_PARTIAL:
            $err_msg = 'Файл завантажено частково. Спробуйте ще раз';
            break;
        case UPLOAD_ERR_NO_FILE:
            $err_msg = 'Файл не надіслано';
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $err_msg = 'Немає тимчасової папки на сервері';
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $err_msg = 'Не вдалося записати файл. Перевірте права на uploads/';
            break;
    }
    echo json_encode(['success' => false, 'message' => $err_msg]);
    exit;
}

$file = $_FILES['file'];

// Перевірка розміру
$max_size = 10 * 1024 * 1024; // 10MB
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'Файл занадто великий (макс 10MB)']);
    exit;
}

// Перевірка типу (через mime, не розширення!)
$allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!in_array($mime, $allowed_mimes, true)) {
    echo json_encode(['success' => false, 'message' => "Непідтримуваний тип файлу: $mime. Дозволено: JPG, PNG, WEBP"]);
    exit;
}

// Розширення
$ext_map = [
    'image/jpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];
$ext = $ext_map[$mime];

// Створюємо папку uploads/ якщо немає
$uploads_dir = PROJECT_ROOT . '/uploads';
if (!is_dir($uploads_dir)) {
    @mkdir($uploads_dir, 0777, true);
}
@chmod($uploads_dir, 0777);

// Унікальне ім'я файлу
$filename = 'photo_' . date('Y-m-d_His') . '_' . substr(bin2hex(random_bytes(4)), 0, 8) . '.' . $ext;
$target_path = $uploads_dir . '/' . $filename;
$relative_path = 'uploads/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $target_path)) {
    echo json_encode(['success' => false, 'message' => 'Не вдалося зберегти файл. Перевірте права на папку uploads/']);
    exit;
}

@chmod($target_path, 0666);

// Видаляємо старий файл якщо вказано
$old_path = $_POST['old_path'] ?? '';
if (!empty($old_path)) {
    $old_real = PROJECT_ROOT . '/' . ltrim($old_path, '/');
    // Безпека: перевіряємо що шлях починається з uploads/ або img/
    $real = realpath($old_real);
    $uploads_real = realpath(PROJECT_ROOT . '/uploads');
    $img_real = realpath(PROJECT_ROOT . '/img');
    if ($real && ($uploads_real || $img_real)) {
        $is_in_uploads = $uploads_real && strpos($real, $uploads_real) === 0;
        $is_in_img = $img_real && strpos($real, $img_real) === 0;
        if ($is_in_uploads || $is_in_img) {
            @unlink($real);
        }
    }
}

echo json_encode([
    'success' => true,
    'path' => $relative_path,
    'filename' => $filename,
    'size' => $file['size'],
    'mime' => $mime,
]);
