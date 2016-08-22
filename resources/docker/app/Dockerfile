FROM php:7.0-cli

RUN apt-get update \
        && apt-get install -y \
        supervisor \
        libsqlite3-dev \
        && docker-php-ext-install pdo \
        && docker-php-ext-install pdo_sqlite

COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY supervisor_app.conf /etc/supervisor/conf.d/app.conf

WORKDIR /srv/aruna

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
