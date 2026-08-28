<?php
require_once __DIR__ . '/data.php';

$current_category = $_GET['id'] ?? '';
$page = basename($_SERVER['SCRIPT_NAME'], '.php'); // 'index' або 'category'
?>
<!DOCTYPE html>
<html lang="uk" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page === 'category' && get_category($current_category)
        ? htmlspecialchars(get_category($current_category)['title']) . ' — ' . htmlspecialchars($SETTINGS['brand_name'] ?? 'krasnobaeva')
        : htmlspecialchars(($SETTINGS['brand_name'] ?? 'krasnobaeva') . ' — ' . ($SETTINGS['brand_tagline'] ?? 'photo & video')) ?></title>
    <meta name="description" content="Весільний та сімейний фотограф і відеограф. Індивідуальні, сімейні та весільні зйомки.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📷</text></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="css/style.css?v=<?= filemtime('css/style.css') ?>" rel="stylesheet">
</head>
<body>

<!-- LOADING SCREEN -->
<div class="loader" id="loader">
    <span class="loader-text">krasnobaeva</span>
</div>

<!-- HEADER -->
<header class="header" id="header">
    <div class="header-inner">
        <a href="index.php" class="logo">krasnobaeva</a>

        <nav class="nav-desktop">
            <a href="index.php" class="nav-link">Головна</a>
            <div class="nav-dropdown">
                <button class="nav-dropdown-btn">Послуги <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
                <div class="dropdown-menu">
                    <?php foreach ($CATEGORIES as $id => $cat): ?>
                        <a href="category.php?id=<?= $id ?>" class="dropdown-link">
                            <img src="<?= $cat['image'] ?>" alt="<?= htmlspecialchars($cat['title']) ?>">
                            <span><?= htmlspecialchars($cat['title']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="index.php#reviews" class="nav-link">Відгуки</a>
            <a href="gallery.php" class="nav-link">Галерея</a>
            <a href="index.php#booking" class="nav-link">Бронювання</a>
            <a href="index.php#faq" class="nav-link">Питання</a>
            <a href="index.php#contacts" class="nav-link">Контакти</a>
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
        </nav>

        <button class="burger" id="burgerBtn" aria-label="Open menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
    </div>
</header>

<!-- Mobile menu -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-header">
        <a href="index.php" class="logo">krasnobaeva</a>
        <button class="mobile-close" id="mobileCloseBtn" aria-label="Close menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <nav class="mobile-nav">
        <a href="index.php" class="mobile-nav-link">Головна</a>
        <div class="mobile-services">
            <p class="mobile-services-label">Послуги</p>
            <div class="mobile-services-grid">
                <?php foreach ($CATEGORIES as $id => $cat): ?>
                    <a href="category.php?id=<?= $id ?>" class="mobile-service-card">
                        <img src="<?= $cat['image'] ?>" alt="<?= htmlspecialchars($cat['title']) ?>">
                        <span><?= htmlspecialchars($cat['title']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <a href="index.php#reviews" class="mobile-nav-link">Відгуки</a>
        <a href="gallery.php" class="mobile-nav-link">Галерея</a>
        <a href="index.php#booking" class="mobile-nav-link">Бронювання</a>
        <a href="index.php#faq" class="mobile-nav-link">Питання</a>
        <a href="index.php#contacts" class="mobile-nav-link">Контакти</a>
        <button class="mobile-theme-toggle" id="mobileThemeToggle">
            <span class="theme-icon">☾</span>
            <span class="theme-label">Темна тема</span>
        </button>
    </nav>
</div>
