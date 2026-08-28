#!/bin/sh
set -e

# Створюємо папку data/ якщо немає
mkdir -p /var/www/html/data
chmod 777 /var/www/html/data 2>/dev/null || true

# Копіюємо seed-файли якщо їх немає або вони порожні
for f in categories.json reviews.json faqs.json bookings.json settings.json admin.json; do
    target="/var/www/html/data/$f"
    if [ ! -f "$target" ] || [ ! -s "$target" ]; then
        if [ -f "/app/seed/$f" ]; then
            cp /app/seed/"$f" "$target"
            echo "[entrypoint] Copied seed: $f"
        else
            echo '[]' > "$target"
            echo "[entrypoint] Created empty: $f"
        fi
    fi
    chmod 666 "$target" 2>/dev/null || true
done

# Запускаємо основну команду (php-fpm)
exec "$@"
