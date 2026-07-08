FROM php:8.2-apache

# Install required system utilities, PHP extensions, and Node.js/NPM
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_pgsql bcmath zip exif intl

# Route Apache traffic to Laravel's public entry point
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copy your codebase into the server environment
COPY . /var/www/html

# Install Composer and optimize your classes for production
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 🌟 FIX: Install node modules and compile production CSS/JS assets
RUN npm install && npm run build

# Give the web server permission to read/write storage caches and database files
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 80

# Automate cache clearing and migrations on every container bootup
CMD ["sh", "-c", "php artisan config:clear && php artisan migrate --force && apache2-foreground"]