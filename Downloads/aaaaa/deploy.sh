#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting deployment process..."

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key if not exists
if [ ! -f .env ]; then
    cp .env.production .env
    php artisan key:generate
fi

# Clear and cache configurations
echo "⚙️ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Optimize autoloader
echo "⚡ Optimizing autoloader..."
composer dump-autoload --optimize

echo "✅ Deployment completed successfully!" 