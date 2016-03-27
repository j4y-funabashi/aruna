DATA_DIR="/media/jayr/aruna"
APP_USER="aruna"

sudo locale-gen en_GB.UTF-8

sudo apt-get update \
    && sudo apt-get install -y \
    nginx \
    ant \
    git \
    supervisor \
    sqlite3 \
    php5-fpm \
    php5-cli \
    php5-imagick \
    php5-curl \
    php5-sqlite


## create data_dir
sudo useradd -G www-data $APP_USER
sudo mkdir -p $DATA_DIR

## bootstrap db
sudo sqlite3 $DATA_DIR/aruna_db.sq3 < /srv/aruna/resources/bootstrap_db.sql

## data dir perms
sudo chown -Rv $APP_USER $DATA_DIR
sudo chgrp -Rv www-data $DATA_DIR;
sudo chmod -Rv g+w $DATA_DIR;

## APP CONFIG
sudo cp /srv/aruna/env.example /srv/.env

## COMPOSER
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

## NGINX PHP CONFIG
sudo cp /srv/aruna/resources/nginx.conf /etc/nginx/sites-enabled/default \
    && sudo cp /srv/aruna/resources/php5-fpm.conf /etc/php5/fpm/pool.d/aruna.conf \
    && sudo cp /srv/aruna/resources/supervisord.conf /etc/supervisor/conf.d/

## RESTART SERVICES
sudo service php5-fpm restart
sudo service nginx restart
sudo service supervisor restart
