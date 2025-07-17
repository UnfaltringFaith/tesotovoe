#!/bin/sh
set -e

# 1) Если нет .env или нет APP_KEY — сгенерировать
if [ ! -f .env ] || ! grep -q APP_KEY .env; then
  cp .env.example .env
  php artisan key:generate
fi

# 2) Проверяем и устанавливаем зависимости composer
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --optimize-autoloader
fi

# 3) Проверяем и устанавливаем зависимости npm
if [ ! -d "node_modules" ]; then
  echo "Installing NPM dependencies..."
  npm install
fi

# 4) Собираем фронтенд если нужно
if [ ! -d "public/build" ]; then
  echo "Building frontend assets..."
  npm run build
fi

# 5) Ждём готовности БД (простая проверка подключения)
until php -r "new PDO('mysql:host=mysql;port=3306;dbname=laravel', 'root', 'password');" >/dev/null 2>&1; do
  echo "Waiting for database…"
  sleep 2
done

echo "Database connection established!"

# 6) Кэш конфигурации для ускорения
php artisan config:cache

# 7) Запускаем PHP-FPM
exec "$@"
