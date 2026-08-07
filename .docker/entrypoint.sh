#!/bin/sh
set -e

# Cache configuration, routes, and views in production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start php-fpm in background and nginx in foreground
php-fpm -D
exec nginx -g "daemon off;"
