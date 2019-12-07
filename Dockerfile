FROM php:7.1

WORKDIR /app
RUN docker-php-ext-install pcntl
