#!/bin/bash
set -e

# Permissions
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# Clear ALL caches
echo "Clearing application cache (Artisan)..."
php -d opcache.enable=0 artisan optimize:clear
php -d opcache.enable=0 artisan view:clear
php -d opcache.enable=0 artisan cache:clear
php -d opcache.enable=0 artisan config:clear
php -d opcache.enable=0 artisan route:clear

# Clear PHP opcache if enabled
if php -r "exit(function_exists('opcache_reset') ? 0 : 1);"; then
    echo "Resetting PHP opcache..."
    php -r "opcache_reset();"
fi

echo "All caches cleared."

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
echo "Publishing Livewire assets..."
php -d opcache.enable=0 artisan livewire:publish --assets --force || true
echo "Publishing Filament assets..."
php -d opcache.enable=0 artisan filament:assets

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
