#!/bin/bash
set -e

#Required to pass environment variables to crontab
printenv | grep -v "no_proxy" > /run/cron-env &&

# Create some temp directories. Had cases, when they were missing despite being in the `Dockerfile`
# It also is required now, since `/tml` is a volume
mkdir -p /tmp/upload/;
mkdir -p /tmp/sessions/;
mkdir -p /tmp/soap/;
mkdir -p /tmp/opcache/;

#We need to start cron service before the endpoint
cron &&

exec docker-php-entrypoint --config /usr/local/etc/php/caddy.json