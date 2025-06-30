# Stage 1: build vendor with Composer
FROM composer:2 AS vendor
WORKDIR /app

# Copy only composer files for leverage cache
COPY composer.json composer.lock ./
# Install dependencies without running scripts (artisan not present yet)
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-scripts

# Stage 2: setup application image
FROM php:8.2-apache

# Instalar dependências de sistema e extensões PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install \
        pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ✅ Instalar o Composer no container final
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar Apache para servir da pasta 'public'
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's|<Directory /var/www/html>|<Directory ${APACHE_DOCUMENT_ROOT}>|g' /etc/apache2/apache2.conf \
    && a2enmod rewrite

# Copiar aplicação
WORKDIR /var/www/html
COPY . .

# Copiar dependências instaladas na primeira stage
COPY --from=vendor /app/vendor ./vendor

# Registrar pacotes Laravel
RUN php artisan package:discover --ansi || true

# Permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]