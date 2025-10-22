#!/bin/bash
# Exit immediately if a command exits with a non-zero status.
set -e

# Set permissions (useful for production volumes)
echo "Setting up storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 1. Clear cache to avoid stale cache issues (like CollisionServiceProvider errors)
# (Using -d opcache.enable=0 ensures we read fresh files, bypassing OPcache)
echo "Clearing application cache..."
php -d opcache.enable=0 artisan optimize:clear

# 2. Wait for the database to be ready
echo "Waiting for database to be ready..."
while ! nc -z db 5432; do
  sleep 0.1
done
echo "Database is ready."

# 3. Generate key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php -d opcache.enable=0 artisan key:generate --force
else
    echo "Application key already exists."
fi

# 4. Run database migrations
echo "Running database migrations..."
php -d opcache.enable=0 artisan migrate --force --no-interaction

# 5. Run seeders
echo "Checking roles and permissions..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder --force --no-interaction || true
echo "Ensuring admin user exists..."
php -d opcache.enable=0 artisan db:seed --class=Database\\Seeders\\AdminSeeder --force --no-interaction

# 6. Create storage link
echo "Creating storage link..."
php -d opcache.enable=0 artisan storage:link

# 7. Publish Filament assets (fixes 404s and JS errors)
echo "Publishing Filament assets..."
php -d opcache.enable=0 artisan filament:assets

# 8. Cache everything (ONLY *after* assets are published)
echo "Caching configuration, routes, and views..."
php -d opcache.enable=0 artisan optimize

echo "Entrypoint tasks complete. Starting container command..."

# 9. Execute the main container command (e.g., "php-fpm")
exec "$@"
