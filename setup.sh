#!/bin/bash

set -eu

if [ "$EUID" -ne 0 ]; then
  echo "Must be run as root/sudo"
  exit 1
fi

if [[ "${SUDO_USER:-}" != "" ]]; then
  echo "Add $SUDO_USER user to www-data group"
  usermod -aG $USER www-data
fi

cp .env.example .env
echo Generate a fresh DB password
PW=$(tr -dc _A-Z-a-z-0-9 < /dev/urandom | head -c10)
echo "DB_PASSWORD=${PW}" > .env
chmod 640 .env

echo Ensure permissions are configured for www-data
chown www-data:www-data -R .

docker-compose build --build-arg PUID=$(id -u www-data) --build-arg PGID=$(id -g www-data) app
