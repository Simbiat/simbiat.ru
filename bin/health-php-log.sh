#!/bin/bash

# Check if there is PHP error log present

set -euo pipefail

DIR="/var/log"
FLAG_FILE="$DIR/error_log.flag"
ERROR_LOG="$DIR/php.log"
LOG_EXCERPT="$(mktemp)"

cleanup() {
    rm -f "$LOG_EXCERPT"
}
trap cleanup EXIT

if [[ -f "${ERROR_LOG}" ]]; then
    if [[ ! -f "$FLAG_FILE" ]]; then
        head -n 100 "$ERROR_LOG" > "$LOG_EXCERPT"
        /etc/supercronic/bin/send-mail.sh "[Alert] Error log found" \
            "$(printf 'A PHP error log was found. It is recommended to check what failed and fix the errors.\n\nFirst 100 lines of the log:\n\n%s' \
                "$(<"$LOG_EXCERPT")")" \
            2
        echo 'Error log found' > "$FLAG_FILE"
    fi
elif [[ -f "$FLAG_FILE" ]]; then
    rm -f "$FLAG_FILE"
fi
