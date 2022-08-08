#!/bin/sh

# This script will repeatedly check if PHP-FPM has started
# (i.e. opened its socket) using exponential backoff. If it has not
# come online after about 30s, the script will abort its attempts.

echo 'Now waiting for PHP-FPM to come online...';

i=0
while [ $i -lt 500 ]
do

    if nc -z 127.0.0.1 9000; then
        echo 'PHP-FPM started listening. Moving on!'
        exit 0
    fi

    if [ `expr $i % 100` -eq 0 ] ; then
        echo 'Still waiting for PHP-FPM to come online...';
    fi

    sleep $(echo ${i} '* 0.00025' | bc)
    i=`expr $i + 1`
done

echo 'Did not detect PHP-FPM to be successfully up and running. Aborting...'
exit 1
