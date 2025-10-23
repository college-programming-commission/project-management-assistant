#!/bin/bash
set -e

# === СИНХРОНІЗАЦІЯ PUBLIC ФАЙЛІВ З ОБРАЗУ ===
echo "Syncing public directory from image..."

# Завжди копіюємо static files якщо їх немає
if [ ! -f /var/www/html/public/index.php ]; then
    echo "Copying core public files..."
    cp -rp /var/www/html-build/public/* /var/www/html/public/ 2>/dev/null || true
fi

# ЗАВЖДИ оновлюємо build/ директорію (Vite assets)
if [ -d /var/www/html-build/public/build ]; then
    echo "Updating Vite build assets..."
    rm -rf /var/www/html/public/build
    cp -rp /var/www/html-build/public/build /var/www/html/public/
    echo "Build assets updated."
fi

echo "Public directory sync complete."
# === КІНЕЦЬ СИНХРОНІЗАЦІЇ ===

# === СПРОЩЕНЕ ОЧИЩЕННЯ ===
echo "Force deleting ALL stale cache files..."
rm -f /var/www/html/bootstrap/cache/*.php
echo "Stale cache files deleted."
# === КІНЕЦЬ ОЧИЩЕННЯ ===

# Permissions
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# Clear cache via Artisan
echo "Clearing application cache (Artisan)..."
php -d opcache.enable=0 artisan optimize:clear

# Wait for DB & Redis
echo "Waiting for database..."
while ! nc -z db 5432; do sleep 0.1; done
echo "Database is ready."
echo "Waiting for Redis..."
while ! nc -z redis 6379; do sleep 0.1; done
echo "Redis is ready."

# Key generation
if [ -z "$APP_KEY" ]; then php -d opcache.enable=0 artisan key:generate --force; fi

# Migrations & Seeders
echo "Running database migrations..."
php -d opcache.enable=0 artisan migrate --force --no-interaction
echo "Checking roles and permissions..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder --force --no-interaction || true
echo "Ensuring admin user exists..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\AdminSeeder --force --no-interaction

# Storage link & Assets
echo "Creating storage link..."
php -d opcache.enable=0 artisan storage:link
echo "Publishing Filament assets..."
php -d opcache.enable=0 artisan filament:assets --force
echo "Publishing all vendor assets..."
php -d opcache.enable=0 artisan vendor:publish --tag=public --force

# Final optimize
echo "Caching configuration, routes, and views..."
php -d opcache.enable=0 artisan optimize

# Setup MinIO (auto-configure public bucket policy)
if [ "${MINIO_AUTO_SETUP:-true}" = "true" ]; then
    echo "Setting up MinIO buckets..."
    php setup-minio.php || echo "Warning: MinIO setup failed, continuing anyway..."
fi

echo "Entrypoint tasks complete. Starting container command..."
exec "$@"
