<?php
require_once dirname(__DIR__) . '/includes/auth.php';

// Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout();
    header('Location: login.php');
    exit;
}

// Already logged in
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;
$is_setup = !is_admin_configured();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($is_setup) {
        // First-time setup
        if (empty($username)) $errors[] = 'Введіть ім\'я користувача';
        if (strlen($password) < 6) $errors[] = 'Пароль має бути не менше 6 символів';
        if ($password !== $password_confirm) $errors[] = 'Паролі не збігаються';

        if (empty($errors)) {
            if (setup_admin($username, $password)) {
                // Auto-login
                $_SESSION['admin_user'] = true;
                $_SESSION['admin_username'] = $username;
                header('Location: index.php?welcome=1');
                exit;
            } else {
                $errors[] = 'Не вдалося зберегти налаштування. Перевірте права на data/admin.json';
            }
        }
    } else {
        // Regular login
        if (try_login($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Невірне ім\'я користувача або пароль';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід — Адмінка krasnobaeva</title>
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="../css/style.css?v=<?= filemtime('../css/style.css') ?>" rel="stylesheet">
    <link href="assets/admin.css?v=<?= filemtime('assets/admin.css') ?>" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-brand">
            <h1>krasnobaeva</h1>
            <p><?= $is_setup ? 'первинне налаштування' : 'адмін-панель' ?></p>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul style="margin:0;padding-left:20px;">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label class="form-label">Ім'я користувача</label>
                <input type="text" name="username" class="form-input" required
                       value="<?= htmlspecialchars($_POST['username'] ?? 'admin') ?>"
                       placeholder="admin">
            </div>
            <div class="form-group">
                <label class="form-label">Пароль</label>
                <input type="password" name="password" class="form-input" required
                       placeholder="••••••••">
            </div>
            <?php if ($is_setup): ?>
                <div class="form-group">
                    <label class="form-label">Підтвердження пароля</label>
                    <input type="password" name="password_confirm" class="form-input" required
                           placeholder="••••••••">
                </div>
                <div class="alert alert-info">
                    Це перший вхід. Придумайте ім'я користувача та пароль — вони будуть використовуватись для входу в адмінку.
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary login-btn">
                <?= $is_setup ? 'Створити адміна' : 'Увійти' ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <div style="text-align:center;margin-top:24px;">
            <a href="../index.php" style="color:#9a9590;font-size:13px;text-decoration:none;">← На головну сайту</a>
        </div>
    </div>
</body>
</html>
