FROM nidorpi/rpi-php:7.0-fpm

RUN apt-get update \
    && apt-get install -y curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename composer
