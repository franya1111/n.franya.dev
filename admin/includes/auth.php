<?php
/**
 * Auth middleware + helpers
 * Використовує PHP-сесії + password_hash.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ADMIN_FILE', __DIR__ . '/../../data/admin.json');

/** Отримати запис адміна з data/admin.json */
function get_admin_record() {
    if (!file_exists(ADMIN_FILE)) return null;
    $data = json_decode(file_get_contents(ADMIN_FILE), true);
    return $data ?: null;
}

/** Зберегти запис адміна */
function save_admin_record($record) {
    return file_put_contents(ADMIN_FILE, json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

/** Перевірити, чи залогінений користувач */
function is_logged_in() {
    return isset($_SESSION['admin_user']) && $_SESSION['admin_user'] === true;
}

/** Спроба входу. Повертає true при успіху. */
function try_login($username, $password) {
    $admin = get_admin_record();
    if (!$admin) return false;
    if ($admin['username'] !== $username) return false;
    if (!password_verify($password, $admin['password_hash'])) return false;
    $_SESSION['admin_user'] = true;
    $_SESSION['admin_username'] = $username;
    return true;
}

/** Вийти */
function logout() {
    $_SESSION = [];
    session_destroy();
}

/** Вимагати логін — редирект на login.php якщо не залогінений */
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/** Перевірити, чи налаштований адмін (для першого запуску) */
function is_admin_configured() {
    $admin = get_admin_record();
    if (!$admin) return false;
    if (empty($admin['password_hash'])) return false;
    if (strpos($admin['password_hash'], 'placeholder') !== false) return false;
    return true;
}

/** Створити/оновити адміна */
function setup_admin($username, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return save_admin_record([
        'username' => $username,
        'password_hash' => $hash,
    ]);
}
