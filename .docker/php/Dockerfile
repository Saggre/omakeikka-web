FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git nano procps libxml2-dev libonig-dev libpng-dev libjpeg-dev libicu-dev \
  && docker-php-ext-install pdo pdo_mysql mysqli zip intl mbstring gd xml opcache \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# install composer in image (optional - composer service exists)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set PHP upload limits
RUN echo "upload_max_filesize = 15M\npost_max_size = 15M" > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html
