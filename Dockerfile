# Stage 1: install dependencies and build vendor
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Stage 2: application image
FROM php:8.2-apache

# Install system libs and PHP extensions (incl. pdo_mysql, pdo_pgsql)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install \
        pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set Apache document root to public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's|<Directory /var/www/html>|<Directory ${APACHE_DOCUMENT_ROOT}>|g' /etc/apache2/apache2.conf \
    && a2enmod rewrite

# Workdir and copy application files
WORKDIR /var/www/html
COPY . .

# Copy built vendor from builder
COPY --from=vendor /app/vendor ./vendor

# Permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expose Apache port and start
EXPOSE 80
CMD ["apache2-foreground"]