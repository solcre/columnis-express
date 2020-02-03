FROM php:5.6-apache

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apt-get update \
    && apt-get install -y zip libzip-dev libxml2-dev libmcrypt-dev libreadline-dev nano git libpng-dev \
    && docker-php-ext-configure zip --with-libzip \
    && pecl install xdebug-2.5.5 \
    && docker-php-ext-install pdo_mysql mysqli mcrypt soap bcmath gd exif \
    && a2enmod rewrite headers \
    && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
    && mv /var/www/html /var/www/public \
    && curl -sS https://getcomposer.org/installer \
        | php -- --install-dir=/usr/local/bin --filename=composer \
    && echo "AllowEncodedSlashes On" >> /etc/apache2/apache2.conf

WORKDIR /var/www