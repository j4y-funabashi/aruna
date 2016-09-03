FROM php:7-fpm

RUN apt-get update \
        && apt-get install -y \
        supervisor \
        libsqlite3-dev \
        && docker-php-ext-install pdo \
        && docker-php-ext-install pdo_sqlite \
        && docker-php-ext-install pcntl

COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY supervisor_app.conf /etc/supervisor/conf.d/app.conf

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

WORKDIR /srv/aruna

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
