FROM composer AS composer

FROM php:7.4.12-cli
RUN apt-get update && apt-get install -y \
  libzip-dev \
  && docker-php-ext-install sockets zip
WORKDIR /app
COPY . .
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]