#!/bin/bash

set -eu

# Prepares a production host. The images come from the registry, so this no
# longer builds anything and the host does not need the app source - only
# this script, docker-compose.yml and the .env it writes.

if [ -e .env ]; then
  echo ".env already exists - refusing to overwrite it."
  exit 1
fi

echo "Generating .env"

# Fixed for the life of the deployment. Rotating it invalidates every
# session, and would orphan any encrypted column.
APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
DB_PASSWORD=$(tr -dc _A-Z-a-z-0-9 < /dev/urandom | head -c32)

cat > .env <<EOF
# Registry holding the images built by .gitea/workflows/ci.yml
IMAGE_REGISTRY=
IMAGE_TAG=latest

APP_KEY=${APP_KEY}
DB_PASSWORD=${DB_PASSWORD}
EOF

chmod 600 .env

echo "Set IMAGE_REGISTRY in .env, then:"
echo "  docker compose pull && docker compose up -d"
