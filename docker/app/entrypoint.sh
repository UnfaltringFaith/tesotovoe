#!/bin/sh
set -e

# 1) Если нет .env или нет APP_KEY — сгенерировать
if [ ! -f .env ] || ! grep -q APP_KEY .env; then
  cp .env.example .env
  php artisan key:generate
fi

# 2) Ждём готовности БД (простая проверка подключения)
until php -r "new PDO('mysql:host=mysql;port=3306;dbname=laravel', 'root', 'password');" >/dev/null 2>&1; do
  echo "Waiting for database…"
  sleep 2
done

echo "Database connection established!"

# 3) Кэш конфигурации для ускорения
php artisan config:cache

# 4) Запускаем PHP-FPM
exec "$@"
