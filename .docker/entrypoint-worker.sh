#!/bin/bash

cd /opt/application

# Correct ownership of the vendor folder (Reason: The mount in docker-compose forces it to root)
sudo chown -R webuser:webuser vendor

i=0
while [ $i -lt 500 ]
do

    if nc -z rabbitmq 5672; then
        exec /usr/local/bin/php artisan rabbitmq:consume --tries=10
    fi

    if [ `expr $i % 100` -eq 0 ] ; then
        echo 'Still waiting for rabbitmq to come online...';
    fi

    sleep $(echo ${i} '* 0.00025' | bc)
    i=`expr $i + 1`
done

echo 'Did not detect rabbitmq to be successfully up and running. Aborting...'
exit 1
