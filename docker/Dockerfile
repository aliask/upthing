# Shared runtime. The vendor, app and test stages all derive from this, so
# composer resolves against exactly the platform the app runs on.
FROM php:8.4-fpm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
      build-essential \
      libicu-dev \
      libzip-dev \
      libpng-dev \
      libjpeg62-turbo-dev \
      libfreetype6-dev \
      libonig-dev \
      libpq-dev \
      locales \
      zip \
      unzip \
      git \
      curl \
      cron && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure zip && \
    docker-php-ext-configure gd --with-freetype && \
    docker-php-ext-install \
        bcmath \
        gd \
        mbstring \
        pcntl \
        intl \
        zip \
        opcache \
        pdo \
        pdo_pgsql

WORKDIR /var/www

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer


# Dependencies resolved from the committed lockfile only - no composer
# update, so the same commit always produces the same dependency tree.
# Copying the manifests before the app code keeps this layer cached.
FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-plugins --no-autoloader \
        --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev --no-interaction


# The image that ships.
FROM base AS app

ARG PUID=33
ARG PGID=33

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

COPY --from=vendor /var/www /var/www

# The suite is only needed by the test stage.
RUN rm -rf /var/www/tests /var/www/phpunit.xml

COPY .env.example .env

RUN echo "59 *    * * *   www-data    php /var/www/artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab

RUN chown -R www-data:www-data /var/www

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]


# Same platform as the app image, but with dev dependencies and the suite,
# so CI exercises what actually ships.
FROM base AS test

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-plugins --no-autoloader \
        --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-interaction

COPY .env.example .env

CMD ["php", "artisan", "test"]
