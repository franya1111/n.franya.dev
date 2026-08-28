FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Розширення PHP для production + curl для Telegram API
RUN apk add --no-cache curl-dev \
    && docker-php-ext-install opcache pdo pdo_mysql curl

# Налаштування PHP для production
RUN echo "memory_limit=128M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "upload_max_filesize=10M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "session.save_path=/tmp" >> /usr/local/etc/php/conf.d/custom.ini

# Копіюємо seed-файли у /app/seed (НЕ перекривається volume)
COPY seed/ /app/seed/

# Стартовий скрипт: гарантує права на запис + копіює seed при першому старті
CMD ["sh", "-c", "\
    mkdir -p /var/www/html/data && chmod 777 /var/www/html/data; \
    for f in categories.json reviews.json faqs.json bookings.json settings.json admin.json; do \
        if [ ! -f /var/www/html/data/$f ] || [ ! -s /var/www/html/data/$f ]; then \
            if [ -f /app/seed/$f ]; then \
                cp /app/seed/$f /var/www/html/data/$f; \
            else \
                case $f in \
                    *.json) echo '[]' > /var/www/html/data/$f ;; \
                esac; \
            fi; \
        fi; \
        chmod 666 /var/www/html/data/$f 2>/dev/null; \
    done; \
    php-fpm \
"]
