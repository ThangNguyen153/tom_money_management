FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libzip-dev

# Extension zip for laravel
RUN docker-php-ext-install zip 

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer global require laravel/installer
RUN mkdir -p /srv/app/web
RUN chown www-data:www-data -R /srv
WORKDIR /srv/app/web