## Stage 1: Build assets
#FROM node:18-alpine as frontend

#WORKDIR /app

#COPY package*.json vite.config.js ./
#COPY resources resources

#RUN npm install
#RUN npm run build

# Stage 2: Laravel Application
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    bash curl zip unzip \
    libjpeg-turbo-dev freetype-dev libpng-dev libzip-dev \
    libxml2-dev oniguruma-dev zlib-dev \
    autoconf build-base pkgconf \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_mysql zip gd opcache \
#    && pecl install redis \
#    && docker-php-ext-enable redis \
    && apk del build-base autoconf pkgconf

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Copy built frontend from previous stage
#COPY --from=frontend /app/public/build /var/www/public/build

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Fix permissions for Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Add OPcache config if available
#COPY docker/app/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Set correct user
USER www-data

CMD ["php-fpm"]
