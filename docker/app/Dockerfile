FROM php:8.2-fpm

# Установка системных зависимостей, PHP-расширений, Node.js и Composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip curl ca-certificates gnupg libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Копируем все файлы проекта
COPY . .

# Копируем конфигурацию PHP-FPM
COPY docker/app/www.conf /usr/local/etc/php-fpm.d/www.conf

# Копируем и настраиваем entrypoint
COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Устанавливаем правильные разрешения
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]