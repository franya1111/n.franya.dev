        <!-- FOOTER -->
        <footer class="footer">
            <div class="container">
                <div class="footer-inner">
                    <div class="footer-social">
                        <a href="<?= htmlspecialchars($SETTINGS['instagram_url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <a href="<?= htmlspecialchars($SETTINGS['telegram_url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Telegram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        </a>
                    </div>
                    <p class="footer-copy">&copy; <?= date('Y') ?> <?= htmlspecialchars($SETTINGS['brand_name']) ?> <?= htmlspecialchars($SETTINGS['brand_tagline']) ?></p>
                    <a href="index.php" class="footer-brand"><?= htmlspecialchars($SETTINGS['brand_name']) ?></a>
                </div>
            </div>
        </footer>
    </div>

    <script src="js/main.js?v=<?= filemtime('js/main.js') ?>"></script>
</body>
</html>
