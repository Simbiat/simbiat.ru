#!/bin/sh

if [ -f /usr/local/logs/crowdsec.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /usr/local/logs/crowdsec.log "/usr/local/logs/crowdsec-$(date -d "yesterday" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /usr/local/logs/crowdsec.log
fi
if [ -f /usr/local/logs/crowdsec_api.log ]; then
    # Copy the log file to the new file with the previous day's date
    cp -p /usr/local/logs/crowdsec_api.log "/usr/local/logs/crowdsec_api-$(date -d "yesterday" +%Y.%m.%d).log"
    # Truncate the original log file
    truncate -s 0 /usr/local/logs/crowdsec_api.log
fi
exit