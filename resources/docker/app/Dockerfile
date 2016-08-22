FROM php:7.0-cli

RUN apt-get update && apt-get install -y supervisor

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /srv/aruna

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
