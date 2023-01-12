FROM wyveo/nginx-php-fpm:php81

WORKDIR /usr/share/nginx/html
EXPOSE 80

# Set timezone
ENV TZ="Europe/Prague"
ENV NODE_OPTIONS="--max-old-space-size=1024"

# Copy project files, Nginx configs & PHP configs
RUN rm -rf ./*
COPY . /usr/share/nginx/html
COPY ./docker/nginx /etc/nginx
COPY ./docker/php/8.1/cli/php.ini /etc/php/8.1/cli/php.ini
COPY ./docker/php/8.1/fpm/php.ini /etc/php/8.1/fpm/php.ini
COPY ./docker/php/8.1/fpm/php.ini /etc/php/8.1/fpm/php-fpm.conf
COPY ./docker/php/8.1/fpm/pool.d/www.conf /etc/php/8.1/fpm/pool.d/www.conf

# Apt packages update
RUN apt-get update && apt-get install -y \
    zip git \
    libicu-dev \
    curl \
    gnupg \
    php8.1-sqlite3

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs && npm i npm -g

# Yarn
RUN corepack enable

# Permissions
RUN chmod -R ugo+w ./temp
RUN chmod -R ugo+w ./log
RUN mkdir -p ./public && chmod -R ugo+r ./public

# Install and build dependencies
RUN composer install --no-cache --prefer-dist --no-scripts && rm -rf /root/.composer
RUN yarn
RUN yarn build

# Remove node_modules & docker folder
RUN rm -rf ./node_modules
RUN rm -rf ./docker

ADD docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Resolve permissions
RUN chown -R www-data:www-data /usr/share/nginx/html

# Set entrypoint file
ENTRYPOINT ["/docker-entrypoint.sh"]