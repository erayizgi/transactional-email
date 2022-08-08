#!/bin/bash

cd /opt/application

# Correct ownership of the vendor folder (Reason: The mount in docker-compose forces it to root)
sudo chown -R webuser:webuser vendor
composer install -q

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf