# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box = "ubuntu/trusty64"
    config.vm.provision :shell, path: "resources/vagrant_bootstrap.sh"
    config.vm.network :forwarded_port, guest: 80, host: 4567
    config.vm.synced_folder ".", "/srv/aruna"
    config.vm.synced_folder "/home/jayr/aruna", "/media/jayr/aruna"
end
