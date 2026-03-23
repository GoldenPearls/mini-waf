FROM php:8.2-fpm-alpine

RUN apk add --no-cache postgresql-dev curl icu-dev \
    && docker-php-ext-install pdo pdo_pgsql intl

# Composer 설치
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

WORKDIR /var/www/html