#!/bin/bash
# ============================================================
# fix-permissions.sh — Виправляє права на data/ для Docker
# ============================================================
#
# ПРОБЛЕМА:
# Папка data/ створена користувачем `art` (uid 1000) на хості.
# PHP-FPM в контейнері працює від `www-data` (uid 82) або root.
# Тому file_put_contents() повертає "Permission denied".
#
# ЗАПУСК (на хості, не в контейнері):
#   cd ~/my-lamp-project/src/n.franya.dev
#   chmod +x fix-permissions.sh
#   ./fix-permissions.sh
#
# Після цього — оновити сторінку в браузері (Ctrl+F5).
# Docker перезавантажувати НЕ треба.
# ============================================================

set -e
cd "$(dirname "$0")"

echo "🔧 Виправляю права на data/ та uploads/..."

# Створюємо папки якщо немає
mkdir -p data
mkdir -p uploads

# Даємо повні права на папки і файли (для Docker — щоб PHP міг писати)
chmod 777 data uploads
chmod 666 data/*.json 2>/dev/null || true
chmod 666 uploads/* 2>/dev/null || true

# Якщо admin.json не існує — створюємо порожній (щоб PHP міг перезаписати)
if [ ! -f data/admin.json ] || [ ! -s data/admin.json ]; then
    echo '{"username":"admin","password_hash":"$2y$10$placeholder_hash_will_be_replaced_on_first_login_setup"}' > data/admin.json
    chmod 666 data/admin.json
    echo "✅ Створено data/admin.json (placeholder hash)"
fi

echo "✅ Готово!"
echo ""
echo "📁 Права встановлено:"
ls -la data/ | head -10
echo "---"
ls -la uploads/ 2>/dev/null | head -5
echo ""
echo "💡 Тепер відкрий https://n.franya.dev/admin/ — має працювати."
