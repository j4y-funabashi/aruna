MEDIA_DIR=/media/jayr/aruna

install_docker () {

    locale-gen en_GB.UTF-8
    sudo apt-get update -q \
        && sudo apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D \
        && echo 'deb https://apt.dockerproject.org/repo ubuntu-xenial main' | sudo tee /etc/apt/sources.list.d/docker.list \
        && sudo apt-get update -q \
        && sudo apt-get install -q -y \
        docker-engine \
        linux-image-extra-$(uname -r) \
        linux-image-extra-virtual \
        apt-transport-https \
        ca-certificates

    curl -L "https://github.com/docker/compose/releases/download/1.8.1/docker-compose-$(uname -s)-$(uname -m)" | sudo tee /usr/local/bin/docker-compose > /dev/null
    sudo chmod +x /usr/local/bin/docker-compose
    sudo usermod -aG docker $USER
}

reset_db () {
    mkdir -p /media/jayr/aruna
    sudo apt-get install -y \
        sqlite3
    sudo sh /vagrant/resources/reset_db.sh

    chown -R www-data /media/jayr/aruna
    chmod -Rv u+rwx /media/jayr/aruna
}

install_webstack () {

    sudo mkdir -p $MEDIA_DIR
    sudo chown $USER $MEDIA_DIR
    sudo chgrp www-data $MEDIA_DIR
    sudo chmod g+w $MEDIA_DIR

    sudo apt-get update -q \
    && sudo apt-get install -qy \
        nginx \
        php7.0-fpm \
        php7.0-cli \
        php7.0-sqlite \
        php7.0-curl \
        php7.0-dom \
        php7.0-zip \
        php-imagick \
        php-xdebug \
        supervisor \
        sqlite3

    sudo cp /srv/aruna/env.example /srv/aruna/.env

    sudo cp /srv/aruna/resources/nginx.conf /etc/nginx/conf.d/default.conf
    sudo cp /srv/aruna/resources/php5-fpm.conf /etc/php/7.0/fpm/pool.d/aruna.conf
    sudo cp /srv/aruna/resources/supervisord.conf /etc/supervisor/supervisord.conf
    sudo cp /srv/aruna/resources/supervisor_app.conf /etc/supervisor/conf.d/aruna.conf

    sudo service nginx restart
    sudo service php7.0-fpm restart
    sudo service supervisor restart
}

install_docker
