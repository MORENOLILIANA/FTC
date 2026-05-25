#!/bin/bash
set -e

# Clear stale bootstrap cache that may reference dev-only packages
rm -f /var/www/bootstrap/cache/services.php
rm -f /var/www/bootstrap/cache/packages.php
rm -f /var/www/bootstrap/cache/config.php
rm -f /var/www/bootstrap/cache/routes-v7.php

# Ensure storage and cache dirs are writable by www-data (php-fpm worker)
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Cache config and routes for production
php artisan config:cache
php artisan route:cache

exec "$@"
