FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install PHP extensions commonly needed for MariaDB
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/

# Update Apache config to use new document root
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html
