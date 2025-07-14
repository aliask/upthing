FROM php:8.2-fpm

ARG APCU_VERSION=5.1.22

# Get frequently used tools
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
      jpegoptim optipng pngquant gifsicle \
      vim \
      git \
      curl \
      wget \
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
        pdo_pgsql && \
    pecl install apcu-${APCU_VERSION} && \
    docker-php-ext-enable apcu

# Copy existing app directory
COPY --chown=33:33 . /var/www
WORKDIR /var/www

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# Copy and run composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install  --no-scripts --no-plugins --no-interaction && \
    composer update

COPY .env.example .env

RUN echo "59 *    * * *   www-data    php /var/www/artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab

RUN chown -R www-data:www-data /var/www

COPY ./docker/entrypoint.sh /

ENTRYPOINT ["/entrypoint.sh"]
