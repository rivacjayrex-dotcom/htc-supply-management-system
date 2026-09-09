#!/usr/bin/env bash
# Exit on error
set -o errexit

echo "--- Installing PHP Composer Dependencies ---"
composer install --no-dev --optimize-autoloader --no-interaction

echo "--- Generating App Key if not present ---"
php artisan key:generate --force || true

echo "--- Clearing & Caching Configurations ---"
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "--- Linking Storage Folder ---"
php artisan storage:link || true

echo "--- Running Database Migrations ---"
php artisan migrate --force

echo "--- Deployment Build Complete! ---"