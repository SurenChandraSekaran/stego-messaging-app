FROM php:8.2-apache

# Install required system utilities and PHP extensions 
# (GD for steganography, libpq-dev for PostgreSQL, libzip-dev for zip utilities)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_pgsql bcmath zip exif

# Route Apache traffic to Laravel's public entry point
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copy your codebase into the server environment
COPY . /var/www/html

# Install Composer and optimize your classes for production
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 💡 Added --no-scripts to prevent Laravel from booting and crashing mid-build
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# Give the web server permission to read/write storage caches
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80