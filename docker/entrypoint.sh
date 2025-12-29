#!/bin/sh
set -e

echo "Initializing application..."

php artisan storage:link || true
php artisan migrate --force
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache

exec "$@"