# Build Laravel Application
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    bash curl zip unzip \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

CMD ["php-fpm"]
