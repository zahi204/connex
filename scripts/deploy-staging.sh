#!/usr/bin/env bash
# Staging deploy script — run on the staging server from the repo root.
#
#   cd /home/deploy/connex
#   ./scripts/deploy-staging.sh
#
# Pulls the latest `develop`, rebuilds, migrates, and clears caches.
# Idempotent — safe to run multiple times.

set -euo pipefail

cd "$(dirname "$0")/.."

COMPOSE="docker compose -f docker-compose.prod.yml --env-file .env.staging"

echo "==> Pulling latest develop"
git fetch origin develop
git reset --hard origin/develop

echo "==> Building images"
$COMPOSE build

echo "==> Starting services"
$COMPOSE up -d

echo "==> Waiting for database"
$COMPOSE exec -T postgres sh -c 'until pg_isready -U "$POSTGRES_USER" -d "$POSTGRES_DB"; do sleep 1; done'

echo "==> Running migrations"
$COMPOSE exec -T app php artisan migrate --force

echo "==> Caching config / routes / views"
$COMPOSE exec -T app php artisan config:cache
$COMPOSE exec -T app php artisan route:cache
$COMPOSE exec -T app php artisan view:cache
$COMPOSE exec -T app php artisan filament:optimize || true

echo "==> Storage symlink"
$COMPOSE exec -T app php artisan storage:link || true

echo "==> Restarting queue, scheduler, and caddy"
$COMPOSE restart queue scheduler caddy

echo "==> Done. https://${APP_DOMAIN:-dev.connex-build.co.il}"
