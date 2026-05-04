#!/bin/bash
set -e

echo "Starting Laravel application initialization..."

# Wait for database to be ready
echo "Waiting for database connection..."
RETRIES=30
until mysql -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --skip-ssl -e "SELECT 1" > /dev/null 2>&1 || [ $RETRIES -eq 0 ]; do
    echo "Waiting for MySQL to be ready, $((RETRIES--)) remaining attempts..."
    sleep 1
done

if [ $RETRIES -eq 0 ]; then
    echo "ERROR: Failed to connect to database after 30 attempts"
    exit 1
fi

echo "Database connection established!"

# Skip APP_KEY validation - .env file is mounted with existing key
echo "Using APP_KEY from mounted .env file"

# Create required storage subdirectories
echo "Creating storage directories..."
mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

# Set storage directory permissions and ownership
echo "Setting storage permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "Laravel initialization complete!"
echo "Starting PHP-FPM..."

# Start PHP-FPM in foreground (it will run as www-data via its own config)
exec php-fpm -F
