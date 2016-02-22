sudo apt-get update \
    && sudo apt-get install -y \
    nginx \
    php5-fpm \
    php5-cli \
    php5-imagick

sudo cp /vagrant/nginx.conf /etc/nginx/sites-enabled/default

sudo service nginx restart
sudo service php5-fpm restart
