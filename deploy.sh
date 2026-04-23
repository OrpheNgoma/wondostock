#!/bin/bash
set -e

echo "==> Deploying WondoStock..."

# Copy docker env if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.docker .env
    echo "  [!] .env created from .env.docker — edit DB_PASSWORD and APP_KEY before continuing"
    exit 1
fi

# Build and start containers
docker compose pull db redis
docker compose build --no-cache app
docker compose up -d

# Wait for DB to be ready
echo "==> Waiting for database..."
sleep 5

# Laravel setup
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan storage:link
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan icons:cache

echo "==> Done! App running on port 8085"
