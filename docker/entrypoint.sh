#!/bin/bash -e

wait-for-it.sh "${DB_HOST}:${DB_PORT}"

if [ $? -ne 0 ]; then
  logger --stderr --priority daemon.warn "waiting for the database to be ready timed out. There may be connection issues." 
fi

cron -f &

php artisan key:generate
php artisan migrate

docker-php-entrypoint php-fpm
