FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Розширення PHP для production + curl для Telegram API
RUN apk add --no-cache curl-dev \
    && docker-php-ext-install opcache pdo pdo_mysql curl

# Налаштування PHP для production + РЕАЛЬНОГО ЧАСУ (щоб git pull одразу працював)
RUN echo "memory_limit=128M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "upload_max_filesize=10M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "session.save_path=/tmp" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/custom.ini

# Копіюємо seed-файли у /app/seed (НЕ перекривається монтуванням)
COPY seed/ /app/seed/

# Окремий стартовий скрипт (надійніше ніж inline CMD)
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
