FROM php:7-fpm

# PHP extensions
RUN apt-get update && apt-get install -y mariadb-client zip unzip git npm vim
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pdo_mysql

# Composer
#RUN apt-get install -y curl software-properties-common
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer