#!/bin/bash

echo "### Update repo"
# Provision virtual machine with development tools
sudo apt-get update && apt-get upgrade

# Dev packages
echo "### Install dev packages"
sudo apt-get install -y vim curl git nodejs npm python-pip

# Install docker for ubuntu 14.04
# https://docs.docker.com/engine/installation/ubuntulinux/

echo "### Install docker"
sudo apt-get install -y --no-install-recommends \
    linux-image-extra-$(uname -r) \
    linux-image-extra-virtual
sudo apt-get -y --no-install-recommends install \ curl \ apt-transport-https \ ca-certificates \ curl \ software-properties-common
sudo apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D
sudo add-apt-repository \
   "deb https://apt.dockerproject.org/repo/ \
   ubuntu-$(lsb_release -cs) \
   main"
sudo apt-get update
sudo apt-get -y install docker-engine
sudo usermod -aG docker vagrant
sudo sh -c "echo 'DOCKER_OPTS=\"--dns 8.8.8.8\"' >> /etc/default/docker"

# Install docker compose
pip install docker-compose

# Install PHP7 and composer
sudo apt-get install -y php-cli php-xdebug php-curl php-mysql php-xml php-zip
sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin creates=/usr/local/bin/composer
sudo mv /usr/local/bin/composer.phar /usr/local/bin/composer

# Install apache
sudo apt-get install -y apache2 libapache2-mod-php
sudo a2enmod rewrite

# Make vagrant folder
sudo mkdir -p /vagrant
sudo chown vagrant.vagrant /vagrant

# Make sure xdebug is working with PHPStorm
echo -n                                 >  /etc/profile.d/xdebug.sh
echo 'export SERVER_NAME=mtools' >> /etc/profile.d/xdebug.sh
echo 'export SERVER_PORT=80'            >> /etc/profile.d/xdebug.sh

# Configure xdebug triggers
echo -n                                 >  /etc/php/7.0/mods-available/xdebug.ini
echo 'zend_extension=xdebug.so'         >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.max_nesting_level = 200'   >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.profiler_enable_trigger = 1'   >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.remote_autostart = 0'  >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.remote_enable = 1' >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.profiler_output_dir = "/vagrant/profiler"' >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.profiler_output_name = "callgrind.out.%t-%R.out"'  >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.remote_connect_back = 0'   >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.idekey=PHPSTORM'   >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.remote_host=10.10.10.1'    >> /etc/php/7.0/mods-available/xdebug.ini
echo 'xdebug.profiler_enable = 0'   >> /etc/php/7.0/mods-available/xdebug.ini

# Configure apache2 virtual host
echo -n                                                     >  /etc/apache2/sites-available/000-default.conf
echo '<VirtualHost *:80>'                                   >> /etc/apache2/sites-available/000-default.conf
echo '    ServerName localhost'                             >> /etc/apache2/sites-available/000-default.conf
echo '    <Directory /vagrant>'                             >> /etc/apache2/sites-available/000-default.conf
echo '        Options Indexes FollowSymLinks'               >> /etc/apache2/sites-available/000-default.conf
echo '        AllowOverride All'                            >> /etc/apache2/sites-available/000-default.conf
echo '        Require all granted'                          >> /etc/apache2/sites-available/000-default.conf
echo '    </Directory>'                                     >> /etc/apache2/sites-available/000-default.conf
echo '    DocumentRoot /vagrant/web'                        >> /etc/apache2/sites-available/000-default.conf
echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'             >> /etc/apache2/sites-available/000-default.conf
echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'  >> /etc/apache2/sites-available/000-default.conf
echo '</VirtualHost>'                                       >> /etc/apache2/sites-available/000-default.conf

# Configure apache2 to run under vagrant user
sed -i -e 's/www-data/vagrant/g' /etc/apache2/envvars

# Restart apache
apache2ctl restart