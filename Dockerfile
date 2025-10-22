# =============================================================================
# Stage 1: Base image with PHP and system dependencies
# =============================================================================
FROM php:8.4-fpm AS base
LABEL maintainer="it_commission_college@uzhnu.edu.ua"
LABEL description="Project Management Assistant - Base image"
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates curl gnupg libzip-dev libpng-dev libjpeg62-turbo-dev \
    libfreetype6-dev libonig-dev libxml2-dev libpq-dev libicu-dev \
    zip unzip git netcat-openbsd \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && NODE_MAJOR=20 \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configure GD library
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install -j$(nproc) pdo_pgsql gd exif pcntl bcmath zip intl opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure PHP opcache
ARG INSTALL_DEV=true
RUN if [ "$INSTALL_DEV" = "true" ]; then \
        echo 'opcache.enable=0' > /usr/local/etc/php/conf.d/opcache.ini; \
    else \
        { \
            echo 'opcache.enable=1'; \
            echo 'opcache.memory_consumption=256'; \
            echo 'opcache.interned_strings_buffer=16'; \
            echo 'opcache.max_accelerated_files=10000'; \
            echo 'opcache.validate_timestamps=0'; \
            echo 'opcache.save_comments=1'; \
            echo 'opcache.fast_shutdown=1'; \
        } > /usr/local/etc/php/conf.d/opcache.ini; \
    fi

# =============================================================================
# Stage 2: Build vendor dependencies
# =============================================================================
FROM base AS vendor
ARG INSTALL_DEV=true
WORKDIR /var/www/html

COPY composer.json composer.lock ./
COPY database/ database/

RUN if [ "$INSTALL_DEV" = "true" ]; then \
        composer install --no-interaction --no-plugins --no-scripts --prefer-dist --optimize-autoloader; \
    else \
        composer install --no-dev --no-interaction --no-plugins --no-scripts --prefer-dist --optimize-autoloader; \
    fi
COPY package*.json ./

# =============================================================================
# Stage 3: Build frontend assets
# =============================================================================
FROM base AS assets
WORKDIR /var/www/html

COPY --from=vendor /var/www/html/vendor/ /var/www/html/vendor/
COPY package*.json ./
COPY . .

RUN npm ci \
    && npm run build \
    && npm cache clean --force \
    && rm -rf node_modules

# =============================================================================
# Stage 4: Final application image
# =============================================================================
FROM base AS app
ARG INSTALL_DEV=true
WORKDIR /var/www/html

COPY --from=vendor /var/www/html/vendor/ /var/www/html/vendor/
COPY --from=assets /var/www/html/public/build/ /var/www/html/public/build/
COPY . .

# Create directories for volumes & cache
RUN mkdir -p /var/www/html/storage/app/public \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache

# === ПОЧАТОК "ЯДЕРНОГО" ВИПРАВЛЕННЯ ===
# "Обдурюємо" Artisan, щоб він не намагався підключитися до БД/Redis під час збірки.
ENV CACHE_DRIVER=file
ENV DB_CONNECTION=sqlite
ENV QUEUE_CONNECTION=sync
ENV SESSION_DRIVER=file
RUN touch /var/www/html/database/database.sqlite

# "Брутально" видаляємо кеш, оскільки `artisan optimize:clear` "падає"
RUN rm -f /var/www/html/bootstrap/cache/*.php

# "Запікаємо" асети та посилання.
# (Тепер це не "впаде", оскільки AdminPanelProvider безпечний)
RUN php -d opcache.enable=0 artisan storage:link
RUN php -d opcache.enable=0 artisan filament:assets

# "Запікаємо" кеш конфігурації (SPA, S3 URL-и тощо)
RUN if [ "$INSTALL_DEV" = "false" ]; then \
        php -d opcache.enable=0 artisan optimize; \
    else \
        echo "Skipping optimization in dev build"; \
    fi

# Очищуємо тимчасові змінні, щоб вони не заважали .env (з Dokploy) під час запуску
ENV CACHE_DRIVER=
ENV DB_CONNECTION=
ENV QUEUE_CONNECTION=
ENV SESSION_DRIVER=
# === КІНЕЦЬ "ЯДЕРНОГО" ВИПРАВЛЕННЯ ===

# Встановлюємо права *після* генерації всіх файлів
RUN chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
        /var/www/html/public \
    && chmod -R 775 \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache

# Copy and setup entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
