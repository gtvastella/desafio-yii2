FROM php:8.2-apache

# Instala dependências e extensões PHP
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

# Habilita o módulo rewrite
RUN a2enmod rewrite

# Copia o Composer para o container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/

# Configura permissões
RUN chown -R www-data:www-data /var/www/ \
    && chmod -R 755 /var/www/

# Adiciona o arquivo de configuração do Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Adiciona o ServerName ao arquivo de configuração
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Exponha a porta padrão do Apache
EXPOSE 80
