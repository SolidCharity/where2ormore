# The codes here are more for Fedora 

Prepare your system
===================

Make sure you have PHP >= 7, and you have npm, composer and MySQL/MariaDB installed.

for example on Fedora 31:
```
  dnf install mariadb-server nginx php php-fpm php-common
  dnf install composer unzip npm git
```

There are tutorials on the web how to configure nginx and php-fpm and MariaDB.

Preparations for shared hosting at Hostsharing
==============================================

At Hostsharing, I followed these instructions for setting it up in shared hosting:

* https://wiki.hostsharing.net/index.php?title=Flarum_installieren#PHP_auf_PHP_7.2_umstellen

```
cd ~/doms/service.my-example-church.de/fastcgi-ssl
cp /usr/local/src/phpstub/phpstub73 .
vi ~/doms/service.my-example-church.de/.htaccess
  AddType application/x-httpd-php73 .php
  Action application/x-httpd-php73 /fastcgi-bin/phpstub73
```

* https://wiki.hostsharing.net/index.php?title=Flarum_installieren#Installation_mit_Composer

```
mkdir ~/composer
cd ~/composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
# check https://getcomposer.org/download/ for the latest valid hash
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

* https://wiki.hostsharing.net/index.php?title=NodeJS

```
# check for latest release at https://github.com/creationix/nvm/releases
wget https://raw.githubusercontent.com/creationix/nvm/v0.35.3/install.sh
chmod a+x install.sh
./install.sh
rm install.sh
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
nvm install 12
```


Setup the application
=====================

For Hostsharing, I need to do this:

```
cd ~/doms/service.my-example-church.de
git clone https://github.com/SolidCharity/where2ormore.git
rm -Rf htdocs-ssl
ln -s where2ormore/public htdocs-ssl
cd where2ormore

cp .env.example .env
# edit the APP URL, and the DB credentials
vi .env

php7.2 ~/composer/composer.phar install
php7.2 ~/composer/composer.phar require doctrine/dbal
php7.2 artisan migrate
php7.2 artisan key:generate
npm install && npm run dev
```
------------------------------

# Setup for Development enviroment :

* Install sqlite database
  ```
    dnf install sqlite
  ```
* Install PHP
  ```
    dnf install php php-common
  ```
* Install composer + npm + git
  ```
  dnf install composer npm git
  ```
* Clone the Repo :
  For this command it is better to run it in the location that you like to save the project files
  ```
  git clone https://github.com/SolidCharity/where2ormore.git
  ```
* create a **sqlite** database
  for this command you should navigate to the location where the you saved the project and then create the **Database file inside there**
  ```
  touch /home/User/file-name/where2ormore/database.sqlite
  ```
----------------

When you start the application the database file is empty . In order for the application to run you need a default user.
Automatically a default user will be created with these credentials as default values **We recommend to change them!**

**user email : demo@example.org**
**user password : demo1234**