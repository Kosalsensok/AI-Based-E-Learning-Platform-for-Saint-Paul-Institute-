#!/bin/sh
set -e

# Run database migrations, seeding, and storage link automatically on container startup
php artisan migrate --force || true
php artisan db:seed --force || true
php artisan storage:link || true

# Cache configuration, routes, and views in production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start php-fpm in background and nginx in foreground
php-fpm -D
exec nginx -g "daemon off;"
