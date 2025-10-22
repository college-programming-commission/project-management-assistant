#!/bin/bash
# Exit immediately if a command exits with a non-zero status.
set -e

# (Ми все ще запускаємо 'chown', це безпечно і корисно
# для вольюму 'app-storage', який підключається під час запуску)
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Wait for database to be ready
echo "Waiting for database to be ready..."
while ! nc -z db 5432; do
  sleep 0.1
done
echo "Database is ready."

# === ЗАВДАННЯ ЧАСУ ВИКОНАННЯ (RUNTIME TASKS) ===

# (Ключ все ще потрібен, на випадок, якщо .env не має його)
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php -d opcache.enable=0 artisan key:generate --force
else
    echo "Application key already exists."
fi

# Run database migrations
echo "Running database migrations..."
php -d opcache.enable=0 artisan migrate --force --no-interaction

# Seed roles and permissions if they don't exist
echo "Checking roles and permissions..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder --force --no-interaction || true

# Create admin user (always runs, idempotent)
echo "Ensuring admin user exists..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\AdminSeeder --force --no-interaction

# === МИ ВИДАЛИЛИ ЗВІДСИ optimize:clear, filament:assets та optimize ===
echo "Entrypoint tasks complete. Starting container command..."

# Execute the main container command (e.g., "php-fpm")
exec "$@"
