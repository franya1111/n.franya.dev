FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Базові розширення (без БД — SQLite не потрібен, контент у PHP-файлах)
RUN docker-php-ext-install opcache

# Налаштування PHP для production
RUN echo "memory_limit=128M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "upload_max_filesize=10M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/custom.ini

CMD ["php-fpm"]
