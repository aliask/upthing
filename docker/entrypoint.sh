#!/bin/bash -e

cron -f &

php artisan key:generate
php artisan migrate

docker-php-entrypoint php-fpm
