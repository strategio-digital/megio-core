#!/bin/sh
# remove temp files
#rm -rf /var/www/html/temp/*

# migrate databases
su-exec www-data php bin/console migration:migrate --no-interaction
su-exec www-data php bin/console orm:generate-proxies
su-exec www-data php bin/console app:auth:resources:update

# start queue workers
echo "Enabled queue workers: $QUEUE_WORKERS_ENABLED"
if [ "$QUEUE_WORKERS_ENABLED" = "true" ]; then
  echo "Starting queue workers..."
  su-exec www-data nohup php bin/console app:queue example.worker >/dev/null 2>&1 &
fi

# start php-fpm and nginx
php-fpm -D && nginx -g 'daemon off;'
