FROM php:8.1-apache

RUN apt-get update \
    && apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-install zip \
    && pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80 


