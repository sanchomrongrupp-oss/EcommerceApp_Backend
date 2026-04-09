# Use official PHP 8.2 CLI image
FROM php:8.2-cli

# Increase Composer memory
ENV COMPOSER_MEMORY_LIMIT=-1

# Set working directory
WORKDIR /var/www

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl unzip libzip-dev libpng-dev libonig-dev libxml2-dev zip \
    libicu-dev g++ libcurl4-openssl-dev pkg-config \
    && docker-php-ext-install intl gd bcmath pcntl mbstring pdo pdo_mysql zip exif xml \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

# Install Composer (latest stable)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Ensure necessary directories exist and set permissions
RUN mkdir -p vendor storage bootstrap/cache \
    && chmod -R 775 vendor storage bootstrap/cache

# Install Laravel dependencies
RUN composer install --no-dev --ignore-platform-reqs
RUN composer dump-autoload --optimize

# Create storage symlink (ignore if already exists)
RUN php artisan storage:link || true

# Expose Render's default port
EXPOSE 10000

# Start Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]