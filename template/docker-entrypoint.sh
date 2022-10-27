#!/bin/sh

# remove temp files
rm -rf /usr/share/nginx/html/temp/*

# migrate databases
php bin/console migration:migrate --no-interaction
php bin/console orm:generate-proxies

# override permissions
chown -R www-data:www-data /usr/share/nginx/html/temp
chown -R www-data:www-data /usr/share/nginx/html/log

# start
/start.sh