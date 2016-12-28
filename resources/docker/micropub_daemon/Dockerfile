FROM php:7-fpm

RUN apt-get update \
        && apt-get install -y \
        libsqlite3-dev \
        && docker-php-ext-install pdo \
        && docker-php-ext-install pdo_sqlite \
        && docker-php-ext-install pcntl \
        && docker-php-ext-install exif

RUN apt-get update \
    && apt-get -y install \
    libmagickwand-dev \
    --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -r /var/lib/apt/lists/*

WORKDIR /srv/aruna
