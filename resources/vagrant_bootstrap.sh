locale-gen en_GB.UTF-8
apt-get update -q
apt-get install -q -y apt-transport-https ca-certificates
apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D
echo deb https://apt.dockerproject.org/repo ubuntu-trusty main > /etc/apt/sources.list.d/docker.list
apt-get update -q
apt-get purge -q -y lxc-docker
apt-get install -q -y --force-yes docker-engine
apt-get install -q -y linux-image-extra-$(uname -r)

curl -fsSL https://github.com/docker/compose/releases/download/1.5.2/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
usermod -aG docker vagrant
