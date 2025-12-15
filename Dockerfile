FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN echo "date.timezone=Europe/Vilnius" > /usr/local/etc/php/conf.d/timezone.ini

WORKDIR /var/www/html

RUN a2enmod rewrite