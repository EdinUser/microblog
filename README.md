# Microblog (Slim 3)

Quick Microblog project, using Slim 3 Micro Framework.

## Instalation

### .ENV settings
Download the repository, copy the content of `.env.example` to `.env` and change the data inside to reflect your needs.

Here is the content of the `.env` file:

```dotenv
# Project name - this is how the Docker container will be named also
CURRENT_PROJECT_NAME=microblog

# Ports for Docker Composer - you can access your project URLs by using these ports
PORT_PHPMYADMIN=8085
PORT_APACHE=4011
PORT_MYSQL=3308

## DB Root to init staff - the root acces for DB
DB_ROOT_USER=root
DB_ROOT_PASS=aaqq11

## DB Data - servername, user, password and DB name
DB_SERVER="db"
DB_USER="root"
DB_PASSWORD="aaqq11"
DB_DB="microblog"

## Is this a demo site? Change this to "PROD" to skip dumps
ENV=DEV

## Root folder - the Docker URL path where the project lies
ROOT_FOLDER="/var/www/html"
```
### Docker
The project runs with Docker. There is a `docker-compose.yml` file in the root folder. The container have the following images:

#### Web
Apache server. It runs from `/docker/webserver/Dockerfile`. The image downloads latest PHP 8.0 and installs some needed packages for PHP.
#### DB
MariaDB DB. The latest official image. The image expects a `db.sql` to be located inside `/docker/mysql` folder to load the structure of the DB to be used.
#### phpMyAdmin
The official latest package of the DB manager

### Composer
If you have installed composer locally, you can run it in the root folder (whre you have downloaded the repo) to install the required packages.

If you do not have PHP 8 and composer on your computer, you can use the official Docker Composer to install the required packages like this:
```shell
docker run --rm --interactive --tty --volume $PWD:/app composer install
```

For the picture upload to work the folders `/temp` and `/public_html/i` should have READ/WRITE access - you can change it to 0777 recursively.

