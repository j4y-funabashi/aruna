DATA_DIR="/tmp/aruna"

sudo locale-gen en_GB.UTF-8

## SORT OUT DATA DIR PERMS
sudo mkdir $DATA_DIR
sudo usermod -aG www-data vagrant;
sudo addgroup www-data;
sudo chgrp -Rv www-data $DATA_DIR;
sudo chmod -Rv g+w $DATA_DIR;

sudo touch /srv/.env

sudo apt-get update \
    && sudo apt-get install -y \
    nginx \
    php5-fpm \
    php5-cli \
    php5-imagick

sudo cp /srv/aruna/nginx.conf /etc/nginx/sites-enabled/default \
    && sudo cp /srv/aruna/php5-fpm.conf /etc/php5/fpm/pool.d/aruna.conf


sudo service php5-fpm restart
sudo service nginx restart
