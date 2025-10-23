#!/bin/bash
set -e

# === СПРОЩЕНЕ ОЧИЩЕННЯ ===
echo "Force deleting ALL stale cache files..."
rm -f /var/www/html/bootstrap/cache/*.php
echo "Stale cache files deleted."
# === КІНЕЦЬ ОЧИЩЕННЯ ===

# Permissions
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

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
php -d opcache.enable=0 artisan filament:assets

# Final optimize
echo "Caching configuration, routes, and views..."
php -d opcache.enable=0 artisan optimize

# Setup MinIO
echo "Setting up MinIO buckets..."
php -d opcache.enable=0 artisan minio:setup || echo "Warning: MinIO setup failed, will retry on next restart"

echo "Entrypoint tasks complete. Starting container command..."
exec "$@"
