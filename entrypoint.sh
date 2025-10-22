#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

php artisan optimize:clear

# Wait for database to be ready
echo "Waiting for database to be ready..."
while ! nc -z db 5432; do
  sleep 0.1
done
echo "Database is ready."

# Generate app key if it doesn't exist
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
else
    echo "Application key already exists."
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force --no-interaction

# Seed roles and permissions if they don't exist
echo "Checking roles and permissions..."
php artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder --force --no-interaction || true

# Create admin user (always runs, idempotent)
echo "Ensuring admin user exists..."
php artisan db:seed --class=Database\\Seeders\\AdminSeeder --force --no-interaction

# Build frontend assets if not present or if build directory is empty
if [ ! -d "public/build" ] || [ -z "$(ls -A public/build 2>/dev/null)" ]; then
    echo "Building frontend assets..."
    npm install
    npm run build
else
    echo "Frontend assets already built."
fi

# Optimize application (only in production with debug off)
if [ "$APP_DEBUG" = "false" ] && [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration, routes, and views..."
    php artisan optimize
else
    echo "Skipping optimization (APP_DEBUG=${APP_DEBUG}, APP_ENV=${APP_ENV})"
fi

# Execute the main container command (e.g., "php-fpm")
exec "$@"