# Stage 1: Node.js build stage for Vite assets
FROM node:18-alpine AS node-builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install npm dependencies
RUN npm install

# Copy application files needed for build
COPY . .

# Build Vite assets
RUN npm run build

# Stage 2: PHP 8.2-FPM runtime
FROM php:8.2-fpm-alpine

# Install build dependencies and system packages
RUN apk add --no-cache --virtual .build-deps \
    gcc \
    g++ \
    make \
    autoconf \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    oniguruma-dev \
    && apk add --no-cache \
    libpng \
    libjpeg-turbo \
    libzip \
    oniguruma \
    mysql-client \
    bash

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mbstring \
    gd \
    zip \
    pcntl \
    fileinfo

# Install Redis extension via PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for dependency installation
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install Composer dependencies (without scripts to avoid needing .env)
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

# Copy application files
COPY --chown=www-data:www-data . .

# Copy compiled Vite assets from Node.js stage
COPY --from=node-builder --chown=www-data:www-data /app/public/build ./public/build

# Generate optimized autoloader without running scripts (scripts will run in entrypoint)
RUN composer dump-autoload --optimize --no-scripts

# Create storage directories and set permissions
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Configure PHP-FPM to run as www-data
RUN sed -i 's/user = www-data/user = www-data/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = www-data/group = www-data/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/listen.owner = www-data/listen.owner = www-data/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/listen.group = www-data/listen.group = www-data/g' /usr/local/etc/php-fpm.d/www.conf

# Clean up build dependencies to reduce image size
RUN apk del .build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

# Install su-exec for user switching
RUN apk add --no-cache su-exec

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose PHP-FPM port
EXPOSE 9000

# Set entrypoint (runs as root, switches to www-data before starting PHP-FPM)
ENTRYPOINT ["docker-entrypoint.sh"]
