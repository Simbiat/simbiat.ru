#!/bin/sh

#Source environments (required for cron)
. /etc/mysql/conf.d/mariadb.env

if [ -f /app/logs/mariadb.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /app/logs/mariadb.log "/app/logs/mariadb-$(date -d "yesterday" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /app/logs/mariadb.log
fi
exit