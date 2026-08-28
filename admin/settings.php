<?php
$page_title = 'Налаштування';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';

    if ($section === 'contacts') {
        $SETTINGS['brand_name'] = trim($_POST['brand_name'] ?? 'krasnobaeva');
        $SETTINGS['brand_tagline'] = trim($_POST['brand_tagline'] ?? 'photo & video');
        $SETTINGS['instagram_url'] = trim($_POST['instagram_url'] ?? '');
        $SETTINGS['telegram_url'] = trim($_POST['telegram_url'] ?? '');
        $SETTINGS['whatsapp_url'] = trim($_POST['whatsapp_url'] ?? '');
        $SETTINGS['phone'] = trim($_POST['phone'] ?? '');
        $SETTINGS['email'] = trim($_POST['email'] ?? '');
        $SETTINGS['about_insta_handle'] = trim($_POST['about_insta_handle'] ?? '');

        $about_raw = $_POST['about_text'] ?? '';
        $about = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r\n", "\n", $about_raw)))));
        $SETTINGS['about_text'] = $about;

        if (save_json('settings.json', $SETTINGS)) {
            $success = '✅ Контакти та загальну інформацію збережено';
        } else { $error = 'Помилка збереження'; }
    } elseif ($section === 'telegram') {
        $SETTINGS['telegram_bot_token'] = trim($_POST['telegram_bot_token'] ?? '');
        $SETTINGS['telegram_chat_id'] = trim($_POST['telegram_chat_id'] ?? '');
        $SETTINGS['notifications_enabled'] = isset($_POST['notifications_enabled']);
        if (save_json('settings.json', $SETTINGS)) {
            $success = '✅ Налаштування Telegram збережено';
        } else { $error = 'Помилка збереження'; }
    } elseif ($section === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $admin = get_admin_record();
        if (!password_verify($current, $admin['password_hash'])) {
            $error = 'Невірний поточний пароль';
        } elseif (strlen($new) < 6) {
            $error = 'Новий пароль має бути не менше 6 символів';
        } elseif ($new !== $confirm) {
            $error = 'Паролі не збігаються';
        } else {
            setup_admin($admin['username'], $new);
            $success = '✅ Пароль змінено';
        }
    }
    // Refresh
    $SETTINGS = load_json('settings.json', []);
}
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<!-- Контакти та загальне -->
<div class="card">
    <h2 class="card-title mb-4">Бренд та контакти</h2>
    <form method="post">
        <input type="hidden" name="section" value="contacts">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Назва бренду</label>
                <input type="text" name="brand_name" class="form-input" value="<?= htmlspecialchars($SETTINGS['brand_name']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Теглайн (під заголовком)</label>
                <input type="text" name="brand_tagline" class="form-input" value="<?= htmlspecialchars($SETTINGS['brand_tagline']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Телефон (для кліку «Подзвонити»)</label>
                <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($SETTINGS['phone']) ?>" placeholder="+380938383871">
            </div>
            <div class="form-group">
                <label class="form-label">Email (сюди прийдуть заявки)</label>
                <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($SETTINGS['email']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Instagram URL</label>
                <input type="url" name="instagram_url" class="form-input" value="<?= htmlspecialchars($SETTINGS['instagram_url']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Telegram URL (особистий)</label>
                <input type="url" name="telegram_url" class="form-input" value="<?= htmlspecialchars($SETTINGS['telegram_url']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">WhatsApp URL</label>
                <input type="url" name="whatsapp_url" class="form-input" value="<?= htmlspecialchars($SETTINGS['whatsapp_url']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Instagram handle (для посилання в блоці «Про мене»)</label>
                <input type="text" name="about_insta_handle" class="form-input" value="<?= htmlspecialchars($SETTINGS['about_insta_handle']) ?>" placeholder="@krasnobaeva.ph">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Текст блоку «Про мене» (кожен абзац з нового рядка)</label>
            <textarea name="about_text" rows="8" class="form-textarea"><?= htmlspecialchars(implode("\n", $SETTINGS['about_text'] ?? [])) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">💾 Зберегти</button>
    </form>
</div>

<!-- Telegram Bot -->
<div class="card">
    <h2 class="card-title mb-4">Telegram Bot (сповіщення про заявки)</h2>
    <div class="alert alert-info">
        <strong>Як налаштувати:</strong><br>
        1. Відкрийте <a href="https://t.me/BotFather" target="_blank" style="color:var(--gold,#c9a96e);">@BotFather</a> → /newbot → отримайте <strong>Bot Token</strong><br>
        2. Додайте бота в чат або групу, напишіть йому будь-що<br>
        3. Перейдіть на <code>https://api.telegram.org/bot&lt;ВАШ_ТОКЕН&gt;/getUpdates</code> — знайдіть <strong>chat.id</strong>
    </div>
    <form method="post">
        <input type="hidden" name="section" value="telegram">
        <div class="form-group">
            <label class="form-label">Bot Token</label>
            <input type="text" name="telegram_bot_token" class="form-input" value="<?= htmlspecialchars($SETTINGS['telegram_bot_token']) ?>" placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
        </div>
        <div class="form-group">
            <label class="form-label">Chat ID</label>
            <input type="text" name="telegram_chat_id" class="form-input" value="<?= htmlspecialchars($SETTINGS['telegram_chat_id']) ?>" placeholder="123456789">
        </div>
        <div class="checkbox-group mb-4">
            <input type="checkbox" name="notifications_enabled" id="notif_enabled" <?= !empty($SETTINGS['notifications_enabled']) ? 'checked' : '' ?>>
            <label for="notif_enabled">Увімкнути сповіщення (Telegram + Email)</label>
        </div>
        <button type="submit" class="btn btn-primary">💾 Зберегти налаштування Telegram</button>
    </form>
    <?php
    $bot_token = $SETTINGS['telegram_bot_token'] ?? '';
    $chat_id = $SETTINGS['telegram_chat_id'] ?? '';
    if (!empty($bot_token) && !empty($chat_id)):
    ?>
        <form method="post" action="test-telegram.php" style="margin-top:16px;">
            <button type="submit" class="btn btn-ghost">📲 Надіслати тестове повідомлення</button>
        </form>
    <?php endif; ?>
</div>

<!-- Зміна пароля -->
<div class="card">
    <h2 class="card-title mb-4">Зміна пароля адміністратора</h2>
    <form method="post">
        <input type="hidden" name="section" value="password">
        <div class="form-group">
            <label class="form-label">Поточний пароль</label>
            <input type="password" name="current_password" class="form-input" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Новий пароль (мін. 6 символів)</label>
                <input type="password" name="new_password" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Підтвердження</label>
                <input type="password" name="confirm_password" class="form-input" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Змінити пароль</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
