FROM php:8.2-apache

# Install system dependencies for CI4 and extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by CodeIgniter 4
RUN docker-php-ext-install intl mysqli gd zip

# Enable Apache mod_rewrite (crucial for CI4 routing)
RUN a2enmod rewrite

# Update Apache configuration to point to the /public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set the working directory
WORKDIR /var/www/html

# Copy the application source code
COPY . .

# Set permissions for the writable folder
# This ensures CI4 can write logs, cache, and session data
RUN chown -R www-data:www-data /var/www/html/writable && \
    chmod -R 775 /var/www/html/writable

# Use the production php.ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Railway provides the PORT environment variable.
# We configure Apache to listen on this port dynamically by replacing all instances of 80.
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/conf-available/docker-php.conf /etc/apache2/sites-available/*.conf /etc/apache2/ports.conf

EXPOSE 80
