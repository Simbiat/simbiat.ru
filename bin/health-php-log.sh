#!/bin/bash

# Check if there is PHP error log present

set -euo pipefail

DIR="/var/log"
FLAG_FILE="$DIR/error_log.flag"
ERROR_LOG="$DIR/php.log"

if [[ -f "${ERROR_LOG}" ]]; then
    if [[ ! -f "$FLAG_FILE" ]]; then
        /etc/supercronic/bin/send-mail.sh "[Alert] Error log found" \
            "A PHP error log was found. It is recommended to check what failed and fix the errors." \
            2
         echo 'Error log found' > "$FLAG_FILE"
    fi
elif [[ -f "$FLAG_FILE" ]]; then
    rm -f "$FLAG_FILE"
fi
