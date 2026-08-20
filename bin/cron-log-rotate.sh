#!/bin/sh
if [ -f /var/log/cron.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /var/log/cron.log "/var/log/cron-$(date -d "@$(( $(date +%s) - 86400 ))" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /var/log/cron.log
fi
exit