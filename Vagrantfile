# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'yaml'

vagrantConfig = YAML.load_file 'Vagrant.config.yml'

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box = "bento/ubuntu-16.10"
    config.vm.network "private_network", ip: vagrantConfig['ip']

    config.vm.synced_folder ".", "/vagrant"

    config.vm.provision :shell, path: "provision.sh"

    config.vm.provider "virtualbox" do |v|
      v.memory = vagrantConfig['vm']['memory']
      v.cpus = vagrantConfig['vm']['cpus']
      v.customize ["modifyvm", :id, "--cableconnected1", "on"]
    end
end