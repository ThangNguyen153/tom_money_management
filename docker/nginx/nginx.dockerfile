FROM nginx:1.10
COPY docker/php/conf.d/dev.ini $PHP_INI_DIR/conf.d/dev-tmm.ini
COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY docker/php/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN mkdir -p /srv/app
WORKDIR /srv/app
