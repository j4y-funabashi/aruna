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
