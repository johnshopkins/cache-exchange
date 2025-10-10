FROM php:8.3-fpm-alpine

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libmemcached-dev \
    zlib-dev \
    cyrus-sasl-dev \
  && apk add --no-cache libmemcached \
  && pecl install memcached \
  && docker-php-ext-enable memcached \
  && apk del .build-deps
