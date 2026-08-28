#!/bin/bash
# ============================================================
# sync.sh — Безпечна синхронізація з GitHub
# ============================================================
#
# Проблема: на сервері локально змінені data/*.json
# (заявки клієнтів через адмінку, правки категорій),
# а git pull не може їх перезаписати.
#
# Рішення:
#   1. Бекап data/*.json у backup-data-<дата>/
#   2. Скидання локальних змін (git checkout)
#   3. git pull — отримуємо нову версію з гіта
#   4. Відновлюємо bookings.json з бекапу (ЗАЯВКИ КЛІЄНТІВ НЕ ВТРАЧАЄМО!)
#   5. categories/reviews/faqs/settings — беруться з гіта (актуальна версія)
#
# Запуск:
#   cd ~/my-lamp-project/src/n.franya.dev
#   chmod +x sync.sh
#   ./sync.sh
# ============================================================

set -e
cd "$(dirname "$0")"

# Кольори для виводу
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔄 Синхронізація krasnobaeva з GitHub${NC}"
echo ""

# 1. Бекап
BACKUP_DIR="backup-data-$(date +%Y%m%d-%H%M%S)"
echo -e "${YELLOW}📁 Створюю бекап у ${BACKUP_DIR}/${NC}"
mkdir -p "$BACKUP_DIR"
cp data/*.json "$BACKUP_DIR/" 2>/dev/null || true
echo -e "   Збережено: $(ls "$BACKUP_DIR" | wc -l) файлів"
echo ""

# 2. Показуємо що змінено локально
echo -e "${YELLOW}📋 Локальні зміни (будуть скинуті):${NC}"
git status --short data/ 2>/dev/null || echo "   (немає)"
echo ""

# 3. Скидаємо локальні зміни у data/*.json
echo -e "${YELLOW}↩️  Скидую локальні зміни data/*.json...${NC}"
git checkout -- data/ 2>/dev/null || true
echo "   ✓ Скинуто"
echo ""

# 4. git pull
echo -e "${YELLOW}⬇️  Завантажую нову версію з GitHub...${NC}"
if git pull origin main; then
    echo -e "   ${GREEN}✓ git pull успішний${NC}"
else
    echo -e "   ${RED}✗ Помилка git pull. Перевірте конфлікти вручну.${NC}"
    echo -e "   Бекап збережено у: ${BACKUP_DIR}"
    exit 1
fi
echo ""

# 5. Відновлюємо bookings.json (ЗАЯВКИ КЛІЄНТІВ!)
if [ -f "$BACKUP_DIR/bookings.json" ]; then
    echo -e "${YELLOW}📥 Відновлюю bookings.json з бекапу (заявки клієнтів)...${NC}"
    cp "$BACKUP_DIR/bookings.json" data/bookings.json
    chmod 666 data/bookings.json
    BOOKINGS_COUNT=$(python3 -c "import json; print(len(json.load(open('data/bookings.json'))))" 2>/dev/null || echo "?")
    echo -e "   ${GREEN}✓ Відновлено заявок: ${BOOKINGS_COUNT}${NC}"
fi
echo ""

# 6. admin.json — відновлюємо з бекапу якщо він там є (пароль адміна!)
if [ -f "$BACKUP_DIR/admin.json" ]; then
    echo -e "${YELLOW}🔐 Відновлюю admin.json (пароль адміна)...${NC}"
    cp "$BACKUP_DIR/admin.json" data/admin.json
    chmod 666 data/admin.json
    echo -e "   ${GREEN}✓ Пароль збережено${NC}"
fi
echo ""

# 7. Відновлюємо права на data/ та uploads/
echo -e "${YELLOW}🔧 Відновлюю права...${NC}"
chmod 777 data uploads 2>/dev/null || true
chmod 666 data/*.json 2>/dev/null || true
chmod 666 uploads/* 2>/dev/null || true
echo -e "   ${GREEN}✓ Готово${NC}"
echo ""

# 8. Фінальний статус
echo -e "${GREEN}✅ Синхронізацію завершено!${NC}"
echo ""
echo -e "${BLUE}📁 Бекап старих даних:${NC} $BACKUP_DIR/"
echo -e "${BLUE}📋 Заявки клієнтів:${NC} збережено"
echo -e "${BLUE}🔐 Пароль адміна:${NC} збережено"
echo ""
echo -e "${YELLOW}💡 categories.json, reviews.json, faqs.json, settings.json${NC}"
echo -e "${YELLOW}   оновлено з гіта (актуальна версія з коду)${NC}"
echo ""
echo -e "${BLUE}🌐 Онови сторінку в браузері (Ctrl+F5)${NC}"
echo -e "${BLUE}   Docker перезавантажувати НЕ потрібно${NC}"
echo ""

# Якщо є бекапи старіше 7 днів — запропонувати видалити
OLD_BACKUPS=$(find . -maxdepth 1 -type d -name "backup-data-*" -mtime +7 2>/dev/null | wc -l)
if [ "$OLD_BACKUPS" -gt 0 ]; then
    echo -e "${YELLOW}🗑 Знайдено $OLD_BACKUPS старих бекапів (старіше 7 днів)${NC}"
    echo -e "${YELLOW}   Видалити? Введіть: ${NC}find . -maxdepth 1 -type d -name 'backup-data-*' -mtime +7 -exec rm -rf {} \\;"
fi
