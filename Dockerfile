FROM composer:latest AS composer-stage

WORKDIR /temp/vendor
COPY /app/composer*.json ./
RUN composer install
RUN rm composer.json composer.lock

FROM php:8.2-apache

WORKDIR /var/www
COPY --from=composer-stage /temp/vendor/ /var/www/
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN rm -rf /temp/vendor/
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd
RUN a2enmod rewrite
RUN mkdir -p /var/www/web/assets && chown -R www-data:www-data /var/www/web/assets && chmod -R 777 /var/www/web/assets
RUN mkdir -p /var/www/runtime && chown -R www-data:www-data /var/www/runtime && chmod -R 777 /var/www/runtime


COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf
EXPOSE 80