# =============================================================================
# Stage 1: Base image with PHP and system dependencies
# =============================================================================
FROM php:8.4-fpm AS base

# Metadata
LABEL maintainer="it_commission_college@uzhnu.edu.ua"
LABEL description="Project Management Assistant - Base image with PHP 8.4 and dependencies"

# Set working directory
WORKDIR /var/www/html

# Install system dependencies in a single layer to reduce image size
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    gnupg \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    netcat-openbsd \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && NODE_MAJOR=20 \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configure GD library with support for JPEG and FreeType
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        gd \
        exif \
        pcntl \
        bcmath \
        zip \
        intl \
        opcache

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure PHP opcache
# For development: opcache disabled for instant file changes
# For production: opcache enabled for performance
#ARG INSTALL_DEV=true
#RUN if [ "$INSTALL_DEV" = "true" ]; then \
#        echo 'opcache.enable=0' > /usr/local/etc/php/conf.d/opcache.ini; \
#    else \
#        { \
#            echo 'opcache.enable=1'; \
#            echo 'opcache.memory_consumption=256'; \
#            echo 'opcache.interned_strings_buffer=16'; \
#            echo 'opcache.max_accelerated_files=10000'; \
#            echo 'opcache.validate_timestamps=0'; \
#            echo 'opcache.save_comments=1'; \
#            echo 'opcache.fast_shutdown=1'; \
#        } > /usr/local/etc/php/conf.d/opcache.ini; \
#    fi

# =============================================================================
# Stage 2: Build vendor dependencies
# =============================================================================
FROM base AS vendor

# Copy only dependency files first for better caching
COPY composer.json composer.lock ./
COPY database/ database/

# Install composer dependencies
# For development, include dev packages
ARG INSTALL_DEV=true
RUN if [ "$INSTALL_DEV" = "true" ]; then \
        composer install \
            --no-interaction \
            --no-plugins \
            --no-scripts \
            --prefer-dist \
            --optimize-autoloader; \
    else \
        composer install \
            --no-dev \
            --no-interaction \
            --no-plugins \
            --no-scripts \
            --prefer-dist \
            --optimize-autoloader; \
    fi

# Copy package files for npm
COPY package*.json ./

# =============================================================================
# Stage 3: Build frontend assets
# =============================================================================
FROM base AS assets

COPY --from=vendor /var/www/html/vendor/ /var/www/html/vendor/
COPY package*.json ./
COPY . .

# Install npm dependencies and build assets
RUN npm ci \
    && npm run build \
    && npm cache clean --force \
    && rm -rf node_modules

# =============================================================================
# Stage 4: Final application image
# =============================================================================
FROM base AS app

# Copy vendor dependencies
COPY --from=vendor /var/www/html/vendor/ /var/www/html/vendor/

# Copy built assets
COPY --from=assets /var/www/html/public/build/ /var/www/html/public/build/

# Copy application code
COPY . .

RUN mkdir -p /var/www/html/storage/app/public \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache

# Set proper permissions
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

# Note: Healthcheck disabled as ps/pgrep not available in minimal PHP-FPM image
# In production, use external monitoring (Kubernetes liveness probes, etc.)

# Expose PHP-FPM port
EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
