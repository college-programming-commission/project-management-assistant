#!/bin/bash
set -e

# === СИНХРОНІЗАЦІЯ PUBLIC ФАЙЛІВ З ОБРАЗУ ===
echo "Syncing public directory from image..."

# Перевірка наявності backup директорії
if [ -d /var/www/html-build/public ]; then
    echo "✓ Backup directory exists: /var/www/html-build/public"
    ls -la /var/www/html-build/public/ | head -10
else
    echo "✗ WARNING: Backup directory NOT found: /var/www/html-build/public"
fi

# Завжди копіюємо static files якщо їх немає
if [ ! -f /var/www/html/public/index.php ]; then
    echo "Copying core public files..."
    cp -rp /var/www/html-build/public/* /var/www/html/public/ 2>/dev/null || true
else
    echo "✓ index.php already exists in volume"
fi

# ЗАВЖДИ оновлюємо build/ директорію (Vite assets)
echo "Checking for Vite build directory..."
if [ -d /var/www/html-build/public/build ]; then
    echo "✓ Found build directory, syncing Vite assets..."
    rm -rf /var/www/html/public/build
    cp -rp /var/www/html-build/public/build /var/www/html/public/
    echo "✓ Vite build assets updated ($(ls -1 /var/www/html/public/build | wc -l) files)"
else
    echo "✗ WARNING: Build directory NOT found at /var/www/html-build/public/build"
    echo "Available in html-build: $(ls -1 /var/www/html-build/ 2>/dev/null || echo 'nothing')"
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
php -d opcache.enable=0 artisan storage:link || true
echo "Publishing Filament assets..."
php -d opcache.enable=0 artisan filament:assets

# Final optimize
echo "Caching configuration, routes, and views..."
php -d opcache.enable=0 artisan optimize

# === ДІАГНОСТИКА VITE BUILD ===
echo "=== Vite Build Assets Check ==="
echo "1. Checking backup in Docker image:"
if [ -d /var/www/html-build ]; then
    echo "   ✓ /var/www/html-build exists"
    ls -lh /var/www/html-build/ | head -10
    if [ -d /var/www/html-build/public ]; then
        echo "   ✓ /var/www/html-build/public exists"
        ls -lh /var/www/html-build/public/ | head -5
        if [ -d /var/www/html-build/public/build ]; then
            echo "   ✓ /var/www/html-build/public/build EXISTS"
            echo "   Build files in image: $(find /var/www/html-build/public/build -type f | wc -l)"
        else
            echo "   ✗ /var/www/html-build/public/build NOT FOUND"
        fi
    else
        echo "   ✗ /var/www/html-build/public NOT FOUND"
    fi
else
    echo "   ✗ /var/www/html-build NOT FOUND"
fi

echo ""
echo "2. Checking current volume:"
if [ -d /var/www/html/public/build ]; then
    echo "   ✓ build/ directory exists in volume"
    echo "   Files count: $(find /var/www/html/public/build -type f | wc -l)"
    echo "   Sample files:"
    ls -lh /var/www/html/public/build/ | head -5
    if [ -f /var/www/html/public/build/manifest.json ]; then
        echo "   ✓ manifest.json found"
    else
        echo "   ✗ manifest.json MISSING"
    fi
else
    echo "   ✗ ERROR: build/ directory NOT found in volume!"
fi
echo "==============================="

# Setup MinIO (auto-configure public bucket policy)
if [ "${MINIO_AUTO_SETUP:-true}" = "true" ]; then
    echo "Setting up MinIO buckets..."
    php setup-minio.php || echo "Warning: MinIO setup failed, continuing anyway..."
fi

echo "Entrypoint tasks complete. Starting container command..."
exec "$@"
