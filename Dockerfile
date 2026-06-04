FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
