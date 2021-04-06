FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libzip-dev
COPY /docker/php/conf.d/dev.ini /usr/local/etc/php/conf.d/conf.d/dev.ini
# Extension mysql driver for mysql
RUN docker-php-ext-install pdo_mysql mysqli
RUN mkdir -p /srv/app/web/app
RUN chown www-data:www-data -R /srv
RUN chmod 755 -R /srv
WORKDIR /srv/app/web/app
#RUN chmod 775 -R /srv/app/web/app/storage
#RUN chmod 775 -R /srv/app/web/app/bootstrap/cache
