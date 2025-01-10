# Use the official PHP latest Alpine image with FPM
FROM php:latest-alpine

# Set working directory
WORKDIR /var/www/html

# Install dependencies for PHP and Laravel
RUN apk --no-cache add \
    libpng-dev \
    libjpeg-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the Laravel application files into the container
COPY . .

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www

# Expose the port the app will run on
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
