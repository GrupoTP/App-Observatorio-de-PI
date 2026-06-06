# Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        zlib1g-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd \
    && apt-get purge -y --auto-remove libpng-dev libjpeg62-turbo-dev zlib1g-dev \
    && apt-get install -y --no-install-recommends libpng16-16t64 libjpeg62-turbo zlib1g \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/php/docker-entrypoint.sh /usr/local/bin/app-entrypoint.sh

RUN chmod +x /usr/local/bin/app-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["app-entrypoint.sh"]
CMD ["apache2-foreground"]
