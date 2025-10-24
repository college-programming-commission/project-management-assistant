#!/bin/bash
set -e

# Permissions
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# Ensure log file exists and is writable
touch /var/www/html/storage/logs/laravel.log
chmod 664 /var/www/html/storage/logs/laravel.log

# Clear ALL caches
echo "Clearing application cache (Artisan)..."
php artisan optimize:clear
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo "All caches cleared."

# Wait for DB & Redis
echo "Waiting for database..."
while ! nc -z db 5432; do sleep 0.1; done
echo "Database is ready."
echo "Waiting for Redis..."
while ! nc -z redis 6379; do sleep 0.1; done
echo "Redis is ready."

# Key generation
if [ -z "$APP_KEY" ]; then php artisan key:generate --force; fi

# Migrations & Seeders
echo "Running database migrations with fresh refresh..."
php artisan migrate:fresh --force --no-interaction

if [ "${APP_ENV}" = "production" ]; then
    echo "Seeding essential data for production..."
    php artisan db:seed --class=Database\\Seeders\\ProductionSeeder --force --no-interaction
else
    echo "Seeding all data for development..."
    php artisan db:seed --force --no-interaction
fi

# Ensure frontend assets exist
echo "Checking for Vite manifest..."
# Build assets if manifest is missing
if [ ! -f /var/www/html/public/build/manifest.json ]; then
    echo "Vite manifest not found, building frontend assets..."
    # Change to the application root directory
    cd /var/www/html
    # Install Node.js dependencies first
    npm ci --silent
    npm run build
    echo "Frontend assets built successfully"
    
    # Ensure manifest is in the expected location
    if [ -f /var/www/html/public/build/.vite/manifest.json ]; then
        cp /var/www/html/public/build/.vite/manifest.json /var/www/html/public/build/manifest.json
        echo "Vite manifest copied from .vite subdirectory to expected location"
    elif [ -f /var/www/html/public/build/manifest.json ]; then
        echo "Vite manifest exists at expected location"
    else
        echo "ERROR: Vite manifest still not found after building assets!"
        ls -la /var/www/html/public/build/ || echo "Build directory does not exist"
        if [ -d /var/www/html/public/build/.vite ]; then
            ls -la /var/www/html/public/build/.vite/
        fi
    fi
else
    echo "Vite manifest found, skipping asset build"
fi

# Storage link & Assets
echo "Creating storage link..."
php artisan storage:link || true
echo "Publishing Livewire assets..."
php artisan livewire:publish --assets --force || true
echo "Publishing Filament assets..."
php artisan filament:assets

# Final optimize
echo "Caching configuration, routes, and views..."
php artisan optimize

# Setup MinIO (auto-configure public bucket policy)
if [ "${MINIO_AUTO_SETUP:-true}" = "true" ]; then
    echo "Setting up MinIO buckets..."
    php setup-minio.php || echo "Warning: MinIO setup failed, continuing anyway..."
fi

echo "Entrypoint tasks complete. Starting container command..."
exec "$@"
