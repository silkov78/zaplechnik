#!/bin/sh

# Awaiting database init
sleep 5

# Generate APP_KEY in .env
php artisan key:generate

# Run migrations and seed
php artisan migrate:fresh --seed

# Start the server
php artisan serve --host=0.0.0.0 --port=8000