#!/bin/sh
set -e

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ -f artisan ]; then
    php artisan key:generate --force --no-interaction >/dev/null 2>&1 || true
fi

exec "$@"
