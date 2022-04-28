FROM composer:2 as vendor

WORKDIR /tmp/

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

FROM php:8.0-apache
RUN a2enmod rewrite
COPY . /var/www/html/
COPY --from=vendor /tmp/vendor/ /var/www/html/vendor/
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/cache/twig
RUN chown www-data:www-data -R /var/www/html/storage