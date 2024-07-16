#!/bin/sh

if [ -f /app/logs/php.log ]; then
    # Copy the log file to the new file with the previous day's date
    mv -f --no-copy -T /app/logs/php.log "/app/logs/php-$(date -d "yesterday" +%Y.%m.%d).log"
fi
exit