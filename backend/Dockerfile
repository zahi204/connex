# syntax=docker/dockerfile:1.7

# ---------- Composer vendor stage ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# ---------- Vite assets stage (Filament theme, app.css, app.js) ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund || npm install --no-audit --no-fund
COPY . .
# Vendor is needed so laravel-vite-plugin can resolve Filament's theme deps
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ---------- Web (PHP-FPM + nginx) ----------
FROM serversideup/php:8.3-fpm-nginx-alpine AS web

USER root

RUN install-php-extensions pdo_pgsql redis gd intl zip bcmath exif

ENV SSL_MODE=off \
    PHP_OPCACHE_ENABLE=1 \
    AUTORUN_ENABLED=false

# App code
COPY --chown=www-data:www-data . /var/www/html
# Vendor from composer stage
COPY --from=vendor --chown=www-data:www-data /app/vendor /var/www/html/vendor
# Built assets from Vite stage
COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build

WORKDIR /var/www/html

# Finish autoload + optimize (needs full source)
RUN composer dump-autoload --optimize --no-dev --no-scripts \
 && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

USER www-data

# ---------- CLI (queue workers, scheduler, artisan one-offs) ----------
FROM serversideup/php:8.3-cli-alpine AS cli

USER root

RUN install-php-extensions pdo_pgsql redis gd intl zip bcmath exif

COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor --chown=www-data:www-data /app/vendor /var/www/html/vendor

WORKDIR /var/www/html

RUN composer dump-autoload --optimize --no-dev --no-scripts \
 && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

USER www-data
