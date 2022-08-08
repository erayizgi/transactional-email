#!/bin/sh

# See: https://gist.github.com/tomazzaman/63265dfab3a9a61781993212fa1057cb

printf "READY\n";

while read line; do
  echo "Processing Event: $line" >&2;
  kill -3 $(cat "/run/supervisord.pid")
done < /dev/stdin
