# Production Dockerfile for Laravel with Pre-built Assets
FROM php:8.3-fpm-alpine

# Install system dependencies & PHP extensions required for Laravel
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev

RUN docker-php-ext-install pdo_mysql mbstring gd zip bcmath intl opcache pcntl posix

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application code (including pre-built assets in public/build)
COPY . .

# Install composer dependencies (production) with ignore platform reqs
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Set directory permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx configuration
COPY .docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy entrypoint script
COPY .docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
