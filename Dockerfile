# Use an official PHP image as the base image
FROM php:7.4-fpm

# Set the working directory
WORKDIR /var/www/app

# Copy the application files
COPY . /var/www/app

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run Composer install
RUN composer install

# Copy the environment file
COPY .env.example .env

# Generate an application key
RUN php artisan key:generate

# Expose port 9000
EXPOSE 9000

# Set the command to run when the container starts
CMD ["php-fpm"]
