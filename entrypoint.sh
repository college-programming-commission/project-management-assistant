#!/bin/bash
# Exit immediately if a command exits with a non-zero status.
set -e

# Налаштування прав доступу
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 1. Чекаємо на базу даних
echo "Waiting for database..."
while ! nc -z db 5432; do
  sleep 0.1
done
echo "Database is ready."

# 2. Чекаємо на Redis
echo "Waiting for Redis..."
while ! nc -z redis 6379; do
  sleep 0.1
done
echo "Redis is ready."

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

# 6. ПЕРЕ-КЕШУЄМО все.
# Це перезапише "file" кеш, згенерований у Dockerfile,
# і створить новий кеш, використовуючи .env (Redis) з Dokploy.
echo "Re-caching configuration for production drivers..."
php -d opcache.enable=0 artisan optimize

echo "Entrypoint tasks complete. Starting container command..."

# 7. Запускаємо головну команду (php-fpm)
exec "$@"
