#!/bin/sh
if [ -f /var/log/ofelia.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /var/log/ofelia.log "/var/log/ofelia-$(date -d "@$(( $(date +%s) - 86400 ))" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /var/log/ofelia.log
fi
exit