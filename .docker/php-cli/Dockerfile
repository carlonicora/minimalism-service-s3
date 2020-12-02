FROM php:7.4-cli-alpine as base

RUN apk update \
    && apk add --no-cache curl g++ make autoconf bash supervisor

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \

COPY ./php-cli/configurations/* /usr/local/etc/php/conf.d/