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

require_once dirname(__DIR__) . '/includes/data.php';

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
$category = trim($input['category'] ?? '');
$package = trim($input['package'] ?? '');
$participants = trim($input['participants'] ?? '');
$date = trim($input['date'] ?? '');
$time = trim($input['time'] ?? '');
$message = trim($input['message'] ?? '');
$is_regular = !empty($input['is_regular']);
$discount_info = trim($input['discount_info'] ?? '');
$contact_method = trim($input['contact_method'] ?? 'phone');
$contact_value = trim($input['contact_value'] ?? '');

// Валідація
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Введіть ім\'я']);
    exit;
}
if (empty($contact_value)) {
    echo json_encode(['success' => false, 'message' => 'Введіть контактні дані']);
    exit;
}

// Створити запис
$booking = [
    'id' => uniqid('bk_', true),
    'name' => mb_substr($name, 0, 100),
    'category' => mb_substr($category, 0, 100),
    'package' => mb_substr($package, 0, 100),
    'participants' => mb_substr($participants, 0, 100),
    'date' => $date,
    'time' => $time,
    'is_regular' => $is_regular,
    'discount_info' => mb_substr($discount_info, 0, 200),
    'contact_method' => mb_substr($contact_method, 0, 20),
    'contact_value' => mb_substr($contact_value, 0, 100),
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
    $text .= "Вид зйомки: {$booking['category']}\n";
    if (!empty($booking['package'])) {
        $text .= "Пакет: {$booking['package']}\n";
    }
    if (!empty($booking['participants'])) {
        $text .= "Кількість учасників: {$booking['participants']}\n";
    }
    $text .= "Бажана дата: " . (!empty($booking['date']) ? $booking['date'] : '—') . "\n";
    if (!empty($booking['time'])) {
        $text .= "Бажаний час: {$booking['time']}\n";
    }
    if ($booking['is_regular']) {
        $text .= "⭐ Постійний клієнт: так\n";
        if (!empty($booking['discount_info'])) {
            $text .= "💰 Знижка: {$booking['discount_info']}\n";
        }
    }
    $contact_label = [
        'phone' => 'Телефон',
        'telegram' => 'Telegram',
        'instagram' => 'Instagram',
    ][$booking['contact_method']] ?? 'Контакт';
    $text .= "Спосіб зв'язку: {$contact_label} — {$booking['contact_value']}\n";
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
    $body .= "Вид зйомки: {$booking['category']}\n";
    if (!empty($booking['package'])) {
        $body .= "Пакет: {$booking['package']}\n";
    }
    if (!empty($booking['participants'])) {
        $body .= "Кількість учасників: {$booking['participants']}\n";
    }
    $body .= "Бажана дата: " . (!empty($booking['date']) ? $booking['date'] : '—') . "\n";
    if (!empty($booking['time'])) {
        $body .= "Бажаний час: {$booking['time']}\n";
    }
    if ($booking['is_regular']) {
        $body .= "⭐ Постійний клієнт: так\n";
        if (!empty($booking['discount_info'])) {
            $body .= "💰 Знижка: {$booking['discount_info']}\n";
        }
    }
    $contact_label = [
        'phone' => 'Телефон',
        'telegram' => 'Telegram',
        'instagram' => 'Instagram',
    ][$booking['contact_method']] ?? 'Контакт';
    $body .= "Спосіб зв'язку: {$contact_label} — {$booking['contact_value']}\n";
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
