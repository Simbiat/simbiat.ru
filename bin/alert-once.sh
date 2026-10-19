#!/bin/bash
# alert-once.sh - send a deduplicated alert, at most once per cooldown window.
#
# Usage: alert-once.sh <stamp_file> <subject> <body>

set -Eeuo pipefail

log() {
    echo "time=\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\" msg=\"$1\"" >&2
}

MAILER="/etc/supercronic/bin/send-mail.sh"
ALERT_STATE_DIR="/var/log/cron"
COOLDOWN=3600                       # seconds between repeated alerts for the same signature
ALERT_LOCK_WAIT=120                 # seconds to wait for a sibling holding the alert lock

stamp="$ALERT_STATE_DIR/$1" subject="$2" body="$3"

mkdir -p "$ALERT_STATE_DIR"

find "$ALERT_STATE_DIR" -type f -name '*' -mtime +7 -delete

(
    LOCK_ACQUIRED=0
    DEADLINE=$(( $(date +%s) + ALERT_LOCK_WAIT ))
    while :; do
        if flock -n 9 2>/dev/null; then
            LOCK_ACQUIRED=1
            break
        fi
        # Lock held by a sibling: poll until it's free or we time out
        [ "$(date +%s)" -ge "$DEADLINE" ] && break
        sleep 2
    done

    if [ "$LOCK_ACQUIRED" -eq 0 ]; then
        log "Alert lock busy for over ${ALERT_LOCK_WAIT}s, assuming sibling handled it"
        exit 0
    fi

    ALERT=1
    if [ -f "$stamp" ]; then
        MTIME=$(stat -c %Y "$stamp" 2>/dev/null || echo 0)   # unreadable stamp -> re-alert
        if [ $(( $(date +%s) - MTIME )) -lt "$COOLDOWN" ]; then
            ALERT=0
        fi
    fi
    if [ "$ALERT" -eq 1 ]; then
        if "$MAILER" "$subject" "$body" 2; then
            printf '%s [%s] %s\n' "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$$" "$body" > "$stamp"
        else
            log "Alert mailer failed for: $subject (no stamp written, will retry next run)"
        fi
    fi
) 9>"$stamp.lock"
