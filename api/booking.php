<?php
/**
 * API endpoint для прийому заявок бронювання.
 * POST: name, phone, category, date, message
 *
 * - Зберігає заявку у data/bookings.json
 * - Якщо налаштований Telegram bot token + chat_id — надсилає повідомлення в Telegram
 * - Якщо налаштований email — надсилає на пошту
 * - Повертає JSON {success: true/false, message: string}
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// CSRF: accept only same-origin requests
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
if ($origin && parse_url($origin, PHP_URL_HOST) !== $host && !empty($host)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden origin']);
    exit;
}

// Отримати дані (підтримка form-data та JSON)
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$category = trim($input['category'] ?? '');
$date = trim($input['date'] ?? '');
$message = trim($input['message'] ?? '');

// Валідація
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Введіть ім\'я']);
    exit;
}
if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Введіть телефон']);
    exit;
}

// Створити запис
$booking = [
    'id' => uniqid('bk_', true),
    'name' => mb_substr($name, 0, 100),
    'phone' => mb_substr($phone, 0, 30),
    'category' => mb_substr($category, 0, 100),
    'date' => $date,
    'message' => mb_substr($message, 0, 2000),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'user_agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 300),
    'status' => 'new',
    'created_at' => date('c'),
];

// Зберегти у bookings.json
$BOOKINGS[] = $booking;
$save_result = save_json('bookings.json', $BOOKINGS);

if (!$save_result) {
    echo json_encode(['success' => false, 'message' => 'Помилка збереження. Спробуйте пізніше.']);
    exit;
}

// Надіслати сповіщення
$notifications_sent = [];
$notifications_failed = [];

// 1. Telegram
$bot_token = $SETTINGS['telegram_bot_token'] ?? '';
$chat_id = $SETTINGS['telegram_chat_id'] ?? '';
if (!empty($bot_token) && !empty($chat_id)) {
    $text = "📷 Нова заявка на бронювання\n\n";
    $text .= "Ім'я: {$booking['name']}\n";
    $text .= "Телефон: {$booking['phone']}\n";
    $text .= "Категорія: {$booking['category']}\n";
    $text .= "Бажана дата: " . (!empty($booking['date']) ? $booking['date'] : '—') . "\n";
    $text .= "Повідомлення: " . (!empty($booking['message']) ? $booking['message'] : '—') . "\n";
    $text .= "\nДата заявки: " . date('d.m.Y H:i');

    $tg_result = send_telegram_message($bot_token, $chat_id, $text);
    if ($tg_result === true) {
        $notifications_sent[] = 'telegram';
    } else {
        $notifications_failed[] = 'telegram: ' . $tg_result;
    }
}

// 2. Email
$email_to = $SETTINGS['email'] ?? '';
if (!empty($email_to)) {
    $subject = "📷 Нова заявка: {$booking['name']} — {$booking['category']}";
    $body = "Нова заявка на бронювання\n\n";
    $body .= "Ім'я: {$booking['name']}\n";
    $body .= "Телефон: {$booking['phone']}\n";
    $body .= "Категорія: {$booking['category']}\n";
    $body .= "Бажана дата: " . (!empty($booking['date']) ? $booking['date'] : '—') . "\n";
    $body .= "Повідомлення: " . (!empty($booking['message']) ? $booking['message'] : '—') . "\n\n";
    $body .= "Дата заявки: " . date('d.m.Y H:i:s') . "\n";
    $body .= "IP: {$booking['ip']}";

    $headers = "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'krasnobaeva.dev') . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (@mail($email_to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers)) {
        $notifications_sent[] = 'email';
    } else {
        $notifications_failed[] = 'email (mail() returned false)';
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Заявку збережено!',
    'booking_id' => $booking['id'],
    'notifications' => [
        'sent' => $notifications_sent,
        'failed' => $notifications_failed,
    ],
]);
exit;

/**
 * Надіслати повідомлення в Telegram через Bot API.
 * Повертає true при успіху або рядок з помилкою.
 */
function send_telegram_message($bot_token, $chat_id, $text) {
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $payload = json_encode([
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true,
    ]);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) return 'curl: ' . $curl_error;
    if ($http_code !== 200) {
        $err = json_decode($response, true);
        return 'http ' . $http_code . ': ' . ($err['description'] ?? 'unknown');
    }
    return true;
}
