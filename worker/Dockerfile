FROM composer AS composer

FROM php:7.4.12-cli
RUN apt-get update && apt-get install -y \
  libzip-dev \
  libpq-dev \
  && docker-php-ext-install sockets zip pdo_pgsql pgsql
WORKDIR /app
COPY . .
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install

CMD ["php", "index.php"]