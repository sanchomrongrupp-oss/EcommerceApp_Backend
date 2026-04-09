FROM php:8.2-cli

# Set Composer memory
ENV COMPOSER_MEMORY_LIMIT=-1

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git curl unzip libzip-dev libpng-dev libonig-dev libxml2-dev zip \
    libicu-dev g++ libcurl4-openssl-dev pkg-config \
    && docker-php-ext-install intl gd bcmath pcntl mbstring pdo pdo_mysql zip exif xml \
    && pecl install mongodb && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Permissions + storage link
RUN chmod -R 775 storage bootstrap/cache
RUN php artisan storage:link || true

# Expose port
EXPOSE 10000

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]