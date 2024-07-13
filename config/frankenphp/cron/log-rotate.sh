#!/bin/sh

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
#Have to do CrowdSec logs here, as well, due to CrowdSec container ignoring custom command setting it up,
#and if I modify the entrypoint, it's not creating CrowdSec own cron tasks for some reason
if [ -f /app/logs/crowdsec.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /app/logs/crowdsec.log "/app/logs/crowdsec-$(date -d "yesterday" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /app/logs/crowdsec.log
fi
if [ -f /app/logs/crowdsec_api.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /app/logs/crowdsec_api.log "/app/logs/crowdsec_api-$(date -d "yesterday" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /app/logs/crowdsec_api.log
fi
exit