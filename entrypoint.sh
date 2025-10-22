#!/bin/bash
# Exit immediately if a command exits with a non-zero status.
set -e

# Налаштування прав доступу
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# === ПОЧАТОК ЗМІН (ЯДЕРНИЙ МЕТОД) ===
# 1. Брутально видаляємо "зомбі-кеш" конфігурації.
# Це найнадійніший спосіб змусити Laravel читати свіжі 'config/*.php' файли.
echo "Force deleting stale config cache..."
rm -f /var/www/html/bootstrap/cache/config.php
echo "Stale config cache deleted."
# === КІНЕЦЬ ЗМІН ===

# 2. Очищуємо решту кешу (про всяк випадок)
echo "Clearing application cache..."
php -d opcache.enable=0 artisan optimize:clear

# 3. Чекаємо на базу даних
echo "Waiting for database to be ready..."
while ! nc -z db 5432; do
  sleep 0.1
done
echo "Database is ready."

# 4. Генеруємо ключ (якщо потрібно)
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php -d opcache.enable=0 artisan key:generate --force
else
    echo "Application key already exists."
fi

# 5. Запускаємо міграції
echo "Running database migrations..."
php -d opcache.enable=0 artisan migrate --force --no-interaction

# 6. Запускаємо сідери
echo "Checking roles and permissions..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder --force --no-interaction || true
echo "Ensuring admin user exists..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\AdminSeeder --force --no-interaction

# 7. Створюємо 'storage link'
echo "Creating storage link..."
php -d opcache.enable=0 artisan storage:link

# 8. Публікуємо асети
echo "Publishing Filament assets..."
php -d opcache.enable=0 artisan filament:assets

# 9. Кешуємо все
# (Тепер він гарантовано прочитає ваші hardcoded-файли і згенерує правильний кеш)
echo "Caching configuration, routes, and views..."
php -d opcache.enable=0 artisan optimize

echo "Entrypoint tasks complete. Starting container command..."

# 10. Запускаємо головну команду (php-fpm)
exec "$@"
