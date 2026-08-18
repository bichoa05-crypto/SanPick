FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y libzip-dev zip unzip git libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli

COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
