# online_training
employee training task

**Setup Installation:**
-----------------------
**Step 1: installing bash on Windows**
**Step 2: installing an Apache HTTP server**
  sudo add-apt-repository ppa:ondrej/apache2
**Once the PPA is configured, update the local package index:**
  sudo apt-get update
**Install Apache:**
 sudo apt-get install apache2
 **Step 2.1: Create a project folder for your web applications. This folder should be outside of the WSL filesystem.The following command I used for my Documents folder.**
  sudo mkdir /mnt/c/Users/YOUR WINDOWS USERNAME/Documents/server
**Create a symbolic link to the selected folder.**  
  sudo ln -s /mnt/c/Users/YOUR WINDOWS USERNAME/Documents/server /var/www/devroot
**Open the Apache default virtual host configuration file:**
  sudo nano /etc/apache2/sites-enabled/000-default.conf
  I did my requried modification in the file
**Step 2.2: Start the Apache HTTP server:**
  sudo service apache2 start
**Step 2.3: Don’t forget to enable Apache modules that are necessary for you.** For example, you can enable mod_rewrite:
sudo a2enmod rewritesudo service apache2 restart

**Step 3: installing the MariaDB server**
Add a repo that contains the latest MariaDB packages:
sudo apt-get install software-properties-common
sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
sudo add-apt-repository 'deb [arch=amd64,i386,ppc64el] http://ams2.mirrors.digitalocean.com/mariadb/repo/10.2/ubuntu xenial main'

**Install MariaDB:**
sudo apt-get updatesudo apt-get install mariadb-server
we will be prompted to create a root password during the installation. Choose a secure password and remember it, because you will need it later.

**Start MariaDB:**

sudo service mysql start
Run the following script (this changes some of the less secure default options):

mysql_secure_installation
**Step 4: installing PHP**
Add PPA for the latest PHP:

sudo add-apt-repository ppa:ondrej/phpsudo apt-get update
**Install PHP 7.1 packages:**

sudo apt-get install php7.1 libapache2-mod-php7.1 php7.1-mcrypt php7.1-mysql php7.1-mbstring php7.1-gettext php7.1-xml php7.1-json php7.1-curl php7.1-zip
We have to restart Apache:

sudo service apache2 restart
Create an info.php file in your project folder with the following content:

<?phpphpinfo();
Open http://localhost/info.php in your browser. If PHP works correctly, we should see one page:

**Step 5: installing phpMyAdmin**
phpMyAdmin is a free and open source administration tool for MySQL and MariaDB.
With phpMyAdmin, you can easily create/manage your databases using a web interface.
sudo apt-get install phpmyadmin

Choose a password for the phpMyAdmin application itself

Enable the necessary PHP extensions:
 sudo phpenmod mcryptsudo phpenmod mbstring
 
Restart Apache:
 sudo service apache2 restart
 
After we can access phpMyAdmin on the following URL: http://localhost/phpmyadmin/
we can login using the root username and the root password you set up during the MariaDB installation.

**Step 6: installing Composer**
Composer is a package manager for PHP. It allows you to install/update the libraries your project depends on. If you are a PHP developer you probably use composer.

Command-line installation
To quickly install Composer in the current directory, we need to run the following script in your terminal. 

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

After Composer has installed successfully, you can install it globally:

sudo mv composer.phar /usr/local/bin/composer

**Now it can be run from any location by typing:**

composer

**Step 7: installing Git:**
Git is a version control system which is primarily used for source code management.
You can install it by running the following command:

sudo apt-get install git

Commandline For creating project:
----------------------------------
1.composer create-project symfony/website-skeleton employee_training

2.Symfony Framework automatically ships with a default directory structure like the one below:

employee_training/
├─ bin/
│  ├─ console
│  └─ phpunit
├─ config/
│  └─ packages/
│  └─ routes/
├─ public/
│  └─ index.php
├─ src/
│  └─ Controller/
│  └─ Entity/
│  └─ Form/
│  └─ Migrations/
│  └─ Repository/
│  └─ Security/
│  └─ Kernel.php
├─ templates/
├─ translations/
├─ var/
├─ vendor
   └─ ...
   
   The recommended purpose for each of these directories can be found below:

bin: Contains the executable files
config: Contains all the configuration defined for any environment
public: This is the document root. It holds all publicly accessible files, such as index.php, stylesheets, JavaScript files, and images. The index.php file is also called “front controller”.
src: Contains all the Symfony-specific code (controllers and forms), your domain code (e.g., Doctrine classes) and all your business logic
templates: Contains all the template files for the application
tests: This houses the files for functional or unit test
translations: Contains translation files for internationalization and localization
var: Contains all the cache and log files generated by the application
vendor: Contains all application dependencies installed by Composer

3.Running the Application
Move into the newly created project and install a web server:

// Change directory
cd employee_training

// install web server
composer require symfony/web-server-bundle 
Then run the application with:

php bin/console server:run

4.Then, we view it on http://localhost:8000.

5.I Used the following  commands to create Entity, Form and Controller

  php bin/console make:user
  php bin/console make:controller ListController
  php bin/console make:form

6.Configuring the Database
  DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7 (Made this changes .env file)
  db_user: Replace with your database username
  db_password: Replace with your database password
  db_name: Replace with your database name. You don't have to create the database yet, as we'll do that in the next step.

7.Next, run the following command to create a database with the value of your database name:

php bin/console doctrine:database:create
At the moment, the database still has no tables. Run the following command that will instruct Doctrine to create the tables based on the User entity that we have created earlier:

php bin/console doctrine:schema:update --force
