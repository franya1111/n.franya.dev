<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

// Беремо функцію з api/booking.php
function _send_telegram($bot_token, $chat_id, $text) {
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
    return ['response' => $response, 'http_code' => $http_code, 'curl_error' => $curl_error];
}

$bot_token = $SETTINGS['telegram_bot_token'] ?? '';
$chat_id = $SETTINGS['telegram_chat_id'] ?? '';

$result_text = '';
$result_type = 'info';

if (empty($bot_token) || empty($chat_id)) {
    $result_text = '❌ Bot Token або Chat ID не налаштовані. Заповніть форму вище.';
    $result_type = 'error';
} else {
    $text = "✅ <b>Тестове повідомлення з адмінки krasnobaeva</b>\n\nЯкщо ви це отримали — Telegram налаштований правильно!\n\nЧас: " . date('d.m.Y H:i:s');
    $r = _send_telegram($bot_token, $chat_id, $text);

    if ($r['curl_error']) {
        $result_text = "❌ Помилка cURL: " . $r['curl_error'];
        $result_type = 'error';
    } elseif ($r['http_code'] === 200) {
        $result_text = "✅ Тестове повідомлення успішно надіслано в Telegram!";
        $result_type = 'success';
    } else {
        $err = json_decode($r['response'], true);
        $result_text = "❌ Telegram API повернув помилку (HTTP {$r['http_code']}): " . ($err['description'] ?? 'невідома помилка');
        $result_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="uk" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест Telegram — Адмінка</title>
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="assets/admin.css" rel="stylesheet">
</head>
<body class="admin-body">
    <div style="max-width:600px;margin:60px auto;padding:0 20px;">
        <div class="card">
            <h1 class="card-title">Тест Telegram</h1>
            <div class="alert alert-<?= $result_type ?>" style="margin-top:16px;"><?= $result_text ?></div>
            <a href="settings.php" class="btn btn-ghost">← Повернутись до налаштувань</a>
        </div>
    </div>
</body>
</html>
