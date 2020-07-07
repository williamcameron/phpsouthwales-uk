FROM php:7.3-fpm-stretch

RUN apt-get update \
    && apt-get -yqq install --no-install-recommends \
      libpng-dev \
      mysql-client \
    && apt-get autoremove -y --purge \
    && rm -fr /var/lib/apt/lists/*

RUN docker-php-ext-install gd opcache pdo pdo_mysql

COPY . /srv/app
COPY tools/docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

WORKDIR /srv/app

RUN chown -R www-data: /srv/app
