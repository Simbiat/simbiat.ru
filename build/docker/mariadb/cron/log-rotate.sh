#!/bin/sh

if [ -f /usr/local/logs/mariadb.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /usr/local/logs/mariadb.log "/usr/local/logs/mariadb-$(date -d "yesterday" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /usr/local/logs/mariadb.log
fi
exit