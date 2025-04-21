FROM php:8.2-apache

# Instala dependências
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Altera o DocumentRoot para a pasta 'public'
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Ativa o mod_rewrite
RUN a2enmod rewrite

# Define permissões
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html && \
    [ -d /var/www/html/storage ] && chmod -R 775 /var/www/html/storage || true && \
    [ -d /var/www/html/bootstrap/cache ] && chmod -R 775 /var/www/html/bootstrap/cache || true
