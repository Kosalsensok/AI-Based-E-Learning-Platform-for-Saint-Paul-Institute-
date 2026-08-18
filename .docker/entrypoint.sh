#!/bin/sh
set -e

# Discover packages & clear stale cached files on startup
php artisan package:discover --ansi || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Run database migrations, seeding, and storage link
php artisan migrate --force || true
php artisan db:seed --force || true
php artisan storage:link || true

# Cache fresh config, routes, and views
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start php-fpm in background and nginx in foreground
php-fpm -D
exec nginx -g "daemon off;"

