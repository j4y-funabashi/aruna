FROM nidorpi/rpi-php:7.0-fpm

RUN apt-get update \
    && apt-get install -y curl \
    git \
    zlib1g-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libpcre3-dev

RUN docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename composer
