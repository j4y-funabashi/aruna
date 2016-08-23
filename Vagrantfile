# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.provider "virtualbox" do |v|
          v.memory = 1024
            v.cpus = 2
    end
    config.vm.box = "ubuntu/trusty64"
    config.vm.provision :shell, path: "resources/vagrant_bootstrap.sh"
end
