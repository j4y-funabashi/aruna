DATA_DIR="/media/BACKUP/aruna"
APP_USER="aruna"

sudo locale-gen en_GB.UTF-8

useradd -G www-data $APP_USER
## SORT OUT DATA DIR PERMS
sudo mkdir -p $DATA_DIR
sudo chown -Rv $APP_USER $DATA_DIR
sudo chgrp -Rv www-data $DATA_DIR;
sudo chmod -Rv g+w $DATA_DIR;

sudo cp /srv/aruna/env.example /srv/.env

sudo apt-get update \
    && sudo apt-get install -y \
    nginx \
    php5-fpm \
    php5-cli \
    php5-imagick \
    php5-curl

sudo cp /srv/aruna/resources/nginx.conf /etc/nginx/sites-enabled/default \
    && sudo cp /srv/aruna/resources/php5-fpm.conf /etc/php5/fpm/pool.d/aruna.conf


sudo service php5-fpm restart
sudo service nginx restart
