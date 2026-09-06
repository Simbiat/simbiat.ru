#!/bin/sh

yesterday="$(date -d "@$(( $(date +%s) - 86400 ))" +%Y.%m.%d)"

rotate_log() {
    file="$1"
    dir="$(dirname "$file")"
    base="$(basename "$file")"

    if [ -f "$file" ]; then
        # Copy the log file to the new file with the previous day's date
        cp -p "$file" "${dir}/${base%.*}-${yesterday}.log"
        # Truncate the original log file
        truncate -s 0 "$file"
    fi
}

rotate_log /var/log/cron.log
rotate_log /var/log/mariadb.log
rotate_log /var/log/crowdsec.log
rotate_log /var/log/mail.log
exit
