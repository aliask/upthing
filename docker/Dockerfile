FROM php:7.4-fpm

ARG APCU_VERSION=5.1.18

# Get frequently used tools
RUN apt-get update && apt-get install -y \
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
    cron

RUN docker-php-ext-configure zip
RUN docker-php-ext-configure gd --with-freetype
RUN docker-php-ext-install \
        bcmath \
        gd \
        mbstring \
        pcntl \
        intl \
        zip \
        opcache \
        pdo \
        pdo_pgsql

# apcu for caching, xdebug for debugging and also phpunit coverage
RUN pecl install \
        apcu-${APCU_VERSION} \
        xdebug \
    && docker-php-ext-enable \
        apcu \
        xdebug


RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# php-cs-fixer tool
RUN wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O /usr/local/bin/php-cs-fixer
RUN chmod +x /usr/local/bin/php-cs-fixer

# Copy existing app directory
COPY . /var/www
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
RUN composer install  --no-scripts --no-plugins --no-interaction

COPY .env.example .env

RUN echo "59 *    * * *   www-data    php /var/www/artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab

RUN chown -R www-data:www-data /var/www

COPY ./docker/entrypoint.sh /
COPY ./docker/wait-for-it.sh /usr/bin/
ENTRYPOINT /entrypoint.sh
