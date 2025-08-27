#!/bin/sh

# Generate application key if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Run migrations and seed
php artisan migrate:fresh --seed

# Start the server
php artisan serve --host=0.0.0.0 --port=8000