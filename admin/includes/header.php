<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/data.php';
require_login();

$current_page = basename($_SERVER['SCRIPT_NAME'], '.php');

function nav_item($page, $label, $icon, $current) {
    $active = $current === $page ? 'active' : '';
    echo "<a href=\"{$page}.php\" class=\"nav-item {$active}\">";
    echo "<svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\">{$icon}</svg>";
    echo "<span>{$label}</span>";
    echo "</a>";
}
?>
<!DOCTYPE html>
<html lang="uk" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмінка — <?= htmlspecialchars($SETTINGS['brand_name'] ?? 'krasnobaeva') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="../css/style.css?v=<?= filemtime('../css/style.css') ?>" rel="stylesheet">
    <link href="assets/admin.css?v=<?= filemtime('assets/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="sidebar">
            <div class="admin-sidebar-header">
                <a href="index.php" class="admin-brand"><?= htmlspecialchars($SETTINGS['brand_name'] ?? 'krasnobaeva') ?></a>
                <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <nav class="admin-nav">
                <?php
                nav_item('index', 'Дашборд', '<rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>', $current_page);
                nav_item('bookings', 'Бронювання', '<path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 16H5V9h14v11z"/>', $current_page);
                nav_item('categories', 'Категорії та ціни', '<path d="M20 7h-9M20 17h-9M14 7V3M14 21v-4M4 7h.01M4 17h.01"/><path d="M5 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM5 13a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>', $current_page);
                nav_item('reviews', 'Відгуки', '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>', $current_page);
                nav_item('faqs', 'FAQ', '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>', $current_page);
                nav_item('settings', 'Налаштування', '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>', $current_page);
                ?>
                <a href="../index.php" class="nav-item nav-item-external" target="_blank">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    <span>Відкрити сайт →</span>
                </a>
            </nav>
            <div class="admin-sidebar-footer">
                <a href="login.php?action=logout" class="nav-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Вийти</span>
                </a>
            </div>
        </aside>

        <!-- Mobile overlay -->
        <div class="admin-overlay" id="adminOverlay"></div>

        <!-- Main content -->
        <div class="admin-main">
            <header class="admin-topbar">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Open menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <h1 class="admin-page-title"><?= $page_title ?? 'Адмінка' ?></h1>
            </header>
            <main class="admin-content">
