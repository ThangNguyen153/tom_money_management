FROM nginx:1.17
COPY /docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY /docker/php/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN  apt-get update \
  && apt-get install -y wget \
  && rm -rf /var/lib/apt/lists/*
RUN set -eux; \
    wget -O /usr/local/bin/mkcert https://github.com/FiloSottile/mkcert/releases/download/v1.4.3/mkcert-v1.4.3-linux-amd64; \
    chmod +x /usr/local/bin/mkcert;
RUN set -eux; \
    mkcert -install; \
    mkdir -p /etc/nginx/ssl; \
    mkcert --cert-file /etc/nginx/ssl/localhost.crt --key-file /etc/nginx/ssl/localhost.key localhost;