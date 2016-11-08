FROM php:7-fpm

RUN apt-get update \
        && apt-get install -y \
        libsqlite3-dev \
        && docker-php-ext-install pdo \
        && docker-php-ext-install pdo_sqlite \
        && docker-php-ext-install pcntl

WORKDIR /srv/aruna
