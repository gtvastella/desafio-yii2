FROM php:8.2-apache

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

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/

COPY ./app/composer.json ./app/composer.lock* /var/www/

RUN if [ ! -d /var/www/vendor ]; then \
    composer install --no-dev --optimize-autoloader; \
    fi

RUN chown -R www-data:www-data /var/www/ \
    && chmod -R 755 /var/www/

COPY apache.conf /etc/apache2/sites-available/000-default.conf

RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

EXPOSE 80
