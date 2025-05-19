# Stage 1: Base PHP-FPM image
FROM php:8.2-fpm-alpine AS base
WORKDIR /var/www
RUN apk add --no-cache \
    bash libpng-dev libxml2-dev zip unzip curl git oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring exif bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Stage 2: Copy source & install dependencies
FROM base AS build
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Stage 3: Final stage
FROM base AS final
WORKDIR /var/www
COPY --from=build /var/www /var/www
CMD ["php-fpm"]
