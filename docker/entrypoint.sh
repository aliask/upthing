#!/bin/bash
set -e

# Let `compose run` and `compose exec` pass a command through instead of
# silently getting php-fpm.
if [ "$#" -gt 0 ]; then
  exec "$@"
fi

cron -f &

# APP_KEY comes from the environment now. Generating one per boot rotated
# the key on every container recreate, which invalidated every session.
if [ -z "${APP_KEY}" ]; then
  echo "APP_KEY is not set - refusing to start." >&2
  exit 1
fi

php artisan migrate --force

exec docker-php-entrypoint php-fpm
