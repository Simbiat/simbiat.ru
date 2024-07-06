#!/bin/sh

#Source environments (required for cron)
. /usr/local/php/config/caddy.env
. /usr/local/php/config/php.env

if [ -f /app/logs/caddy.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /app/logs/caddy.log "/app/logs/caddy-$(date -d "yesterday" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /app/logs/caddy.log
fi
if [ -f /app/logs/php.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /app/logs/php.log "/app/logs/php-$(date -d "yesterday" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /app/logs/php.log
fi
exit