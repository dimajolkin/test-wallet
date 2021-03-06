FROM php:7.1

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

WORKDIR /app
RUN docker-php-ext-install pcntl
RUN apt-get update && apt-get install -y zlib1g-dev \
    && docker-php-ext-install zip
