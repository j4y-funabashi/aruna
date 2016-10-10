install_docker () {

    locale-gen en_GB.UTF-8

    sudo apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D
    echo 'deb https://apt.dockerproject.org/repo ubuntu-trusty main' | sudo tee /etc/apt/sources.list.d/docker.list
    sudo apt-get update -q \
        && sudo apt-get install -q -y --force-yes \
        docker-engine \
        linux-image-extra-$(uname -r) \
        apt-transport-https \
        ca-certificates

    curl -fsSL https://github.com/docker/compose/releases/download/1.8.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
    usermod -aG docker vagrant
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
    sudo apt-get update -q \
    && sudo apt-get install -qy \
        nginx \
        php5-fpm

    sudo cp nginx.conf /etc/nginx/conf.d/default.conf
    sudo cp php5-fpm.conf /etc/php5/fpm/pool.d/aruna.conf
}

#install_docker
#reset_db
install_webstack
