#!/usr/bin/env bash
# Zero-surprise production deploy for Creative Store.
# Run from the project root on the server:  bash bin/deploy.sh
set -euo pipefail

echo "→ Maintenance mode"
php artisan down --retry=15 || true

echo "→ Code and dependencies"
git pull --ff-only
composer install --no-dev --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build

echo "→ Database"
php artisan migrate --force

echo "→ Caches"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan storage:link || true

echo "→ Restart workers"
php artisan queue:restart

echo "→ Live"
php artisan up
