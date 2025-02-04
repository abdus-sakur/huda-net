FROM php:8.3-fpm-alpine3.20

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    icu-dev \
    libzip-dev

RUN docker-php-ext-install \
    pdo_mysql \
    gd \
    xml \
    bcmath \
    intl \
    opcache \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer dump-autoload --optimize
RUN composer install --no-interaction --no-plugins --no-scripts

# kusus untuk filament
RUN php artisan vendor:publish --force --tag=livewire:assets
RUN php artisan filament:optimize

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]