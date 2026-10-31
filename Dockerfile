FROM php:8.2-fpm-alpine

# Install dependencies & PHP extensions
RUN apk add --no-cache nginx supervisor curl libpng-dev libzip-dev zip unzip git \
    && docker-php-ext-install pdo pdo_mysql bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files
COPY . .

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php-fpm"]