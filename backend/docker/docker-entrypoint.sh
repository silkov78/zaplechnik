#!/bin/bash

# Awaiting database init
sleep 5

# Check if .env file exists, create from .env.example if not
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        echo "ENTRYPOINT: Creating .env file from .env.example..."
        cp .env.example .env
    else
        echo "ENTRYPOINT: Warning: No .env or .env.example file found!"
        exit 1
    fi
else
    echo "ENTRYPOINT: .env file already exists, skipping creation..."
fi

# Generate APP_KEY only if it doesn't exist or is empty
if ! grep -q "^APP_KEY=.\+" .env 2>/dev/null; then
    echo "ENTRYPOINT: Generating application key..."
    php artisan key:generate
else
    echo "ENTRYPOINT: APP_KEY already exists in .env, skipping generation..."
fi

# Run migrations and seed
php artisan migrate:fresh --seed

# Start the server
php artisan serve --host=0.0.0.0 --port=8000
