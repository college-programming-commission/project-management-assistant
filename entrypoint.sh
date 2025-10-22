#!/bin/bash
# Exit immediately if a command exits with a non-zero status.
set -e

# Налаштування прав доступу (корисно для вольюмів)
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 1. Очищуємо кеш, щоб уникнути помилок `CollisionServiceProvider`
# (Використовуємо -d opcache.enable=0 для гарантії)
echo "Clearing application cache..."
php -d opcache.enable=0 artisan optimize:clear

# 2. Чекаємо на базу даних
echo "Waiting for database to be ready..."
while ! nc -z db 5432; do
  sleep 0.1
done
echo "Database is ready."

# 3. Генеруємо ключ (якщо потрібно)
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php -d opcache.enable=0 artisan key:generate --force
else
    echo "Application key already exists."
fi

# 4. Запускаємо міграції
echo "Running database migrations..."
php -d opcache.enable=0 artisan migrate --force --no-interaction

# 5. Запускаємо сідери
echo "Checking roles and permissions..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder --force --no-interaction || true
echo "Ensuring admin user exists..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\AdminSeeder --force --no-interaction

# === ПОЧАТОК ЗМІН ===
# 6. Створюємо 'storage link'
echo "Creating storage link..."
php -d opcache.enable=0 artisan storage:link

# 7. Публікуємо асети (для виправлення помилок 404 та JS)
echo "Publishing Filament assets..."
php -d opcache.enable=0 artisan filament:assets
# === КІНЕЦЬ ЗМІН ===

# 8. Кешуємо все (ТІЛЬКИ ПІСЛЯ того, як асети опубліковані)
echo "Caching configuration, routes, and views..."
php -d opcache.enable=0 artisan optimize

echo "Entrypoint tasks complete. Starting container command..."

# 9. Запускаємо головну команду (php-fpm)
exec "$@"
