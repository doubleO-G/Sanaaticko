# Use the official PHP Alpine Linux image as a parent image
FROM php:8.3.7-fpm-alpine

# Set working directory
WORKDIR /var/www/html


# Install system dependencies
RUN apk update && \
    apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    curl \
    nodejs \
    npm \
    && rm -rf /var/cache/apk/*


# Install PHP extensions
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip mysqli


# Set permissions
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN chown -R www-data:www-data /var/www/html



# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer.json and composer.lock
# COPY composer.json composer.lock ./

# Copy the rest of the application
COPY . .

# Install application dependencies

 # Check directory contents before `composer install`
RUN ls -la /var/www/html 

RUN composer install --no-scripts --no-autoloader --verbose

 # Check directory contents after `composer install`
RUN ls -la /var/www/html 


# Generate optimized autoload files
RUN composer dump-autoload --optimize


# Expose port 9000 to communicate with Nginx or other web server
# EXPOSE 9000


# Start PHP-FPM
CMD ["php-fpm", "-F"]
# CMD ["php", "artisan", "serve"]