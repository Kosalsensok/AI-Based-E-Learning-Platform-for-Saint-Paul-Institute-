# Production Dockerfile for Laravel with Pre-built Assets
FROM php:8.3-fpm-alpine

# Install system dependencies & PHP extensions required for Laravel
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev

RUN docker-php-ext-install pdo_mysql mbstring gd zip bcmath intl opcache pcntl posix

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure Composer environment for resilient network downloads & avoid GitHub Rate Limits
ARG GITHUB_TOKEN
ENV GITHUB_TOKEN=$GITHUB_TOKEN \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_PROCESS_TIMEOUT=600 \
    COMPOSER_MEMORY_LIMIT=-1

WORKDIR /var/www/html

# Copy composer files first for optimal Docker layer caching
COPY composer.json composer.lock ./

# Configure GitHub OAuth token if provided, and install composer dependencies with fallback to --prefer-source on rate limits
RUN if [ -n "$GITHUB_TOKEN" ]; then composer config -g github-oauth.github.com "$GITHUB_TOKEN"; fi \
    && ( \
        composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs --prefer-dist --no-scripts \
        || composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs --prefer-source --no-scripts \
    )

# Copy remaining application code (including pre-built assets in public/build)
COPY . .

# Generate optimized classmap & autoloader without triggering runtime artisan scripts during build
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative --no-scripts

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

