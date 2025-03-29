FROM php:8.2-apache

# Instala dependências
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Ativa o mod_rewrite
RUN a2enmod rewrite

# Copia o .htaccess padrão do Laravel
COPY . /var/www/html

# Define permissões
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
