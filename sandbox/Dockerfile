FROM php:8.2-fpm-alpine as build-stage-php
WORKDIR /build

RUN apk add curl

COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-cache --prefer-dist --no-scripts

FROM node:18-alpine as build-stage-node
WORKDIR /build

COPY ./assets ./assets
COPY ./package.json ./package.json
COPY ./yarn.lock ./yarn.lock
COPY ./vite.config.ts ./vite.config.ts
COPY ./tsconfig.node.json ./tsconfig.node.json
COPY ./tsconfig.json ./tsconfig.json
COPY --from=build-stage-php /build/vendor /build/vendor

RUN yarn && yarn build

FROM php:8.2-fpm-alpine
WORKDIR /var/www/html

# Set timezone
ENV TZ="Europe/Prague"

# Install linux dependencies
RUN apk add openssl curl ca-certificates
RUN apk add bash nano
RUN apk add nginx
RUN apk add libpq-dev

# Nginx & PHP configs
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/http.d/default.conf /etc/nginx/http.d/default.conf
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/php.ini
#COPY ./docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# PHP extensions https://github.com/mlocati/docker-php-extension-installer#supported-php-extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql
RUN docker-php-ext-install opcache

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy source code & set permissions
COPY . ./
COPY --from=build-stage-php /build/vendor ./vendor
COPY --from=build-stage-node /build/public ./public

# Resolve permissions
RUN chmod -R ugo+w ./temp
RUN chmod -R ugo+w ./log
RUN chmod -R ugo+r ./public
RUN chown -R www-data:www-data /var/www/html

# Add entrypoint
ADD ./docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]