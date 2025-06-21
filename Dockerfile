# Stage 1: build vendor with Composer
FROM composer:2 AS vendor
WORKDIR /app

# Copy only composer files for leverage cache
COPY composer.json composer.lock ./
# Install dependencies without running scripts (artisan not present yet)
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-scripts

# Stage 2: setup application image
FROM php:8.2-apache

# Install system libs and PHP extensions (MySQL + PostgreSQL)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install \
        pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure Apache document root to 'public'
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's|<Directory /var/www/html>|<Directory ${APACHE_DOCUMENT_ROOT}>|g' /etc/apache2/apache2.conf \
    && a2enmod rewrite

# Set working directory and copy application
WORKDIR /var/www/html
COPY . .

# Copy built vendor directory
COPY --from=vendor /app/vendor ./vendor

# Run Laravel package discovery (artisan now present)
RUN php artisan package:discover --ansi

# Set proper permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expose port and start Apache
EXPOSE 80
CMD ["apache2-foreground"]
