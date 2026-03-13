FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath gd zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dosya yükleme limitleri (Filament/Livewire varyasyon görselleri için)
COPY docker/php/upload-limits.ini /usr/local/etc/php/conf.d/upload-limits.ini

WORKDIR /var/www