#!/bin/sh

STATE_DIR="/var/log"
COOLDOWN=3600   # seconds between repeated alerts for the same call

send_alert() {
    /etc/supercronic/bin/send-mail.sh "[Alert] Failed cron job" \
        "Cron job \`$CONTAINER: $*\` failed" \
        2
    echo "Cron job \`$CONTAINER: $*\` failed" > "$STAMP"
}

if [ $# -lt 2 ]; then
    log "Usage: $0 <container_name> <command>"
    exit 1
fi

CONTAINER="$1"
shift

# unique signature per container + command combination
SIG=$(printf '%s|%s' "$CONTAINER" "$*" | md5sum | cut -c1-12)
STAMP="$STATE_DIR/cron_fail-$SIG"

STATE=$(docker inspect --format '{{.State.Status}}' "$CONTAINER" 2>/dev/null)

if [ -z "$STATE" ]; then
    log "Container '$CONTAINER' not found."
    exit 1
fi

if [ "$STATE" != "running" ]; then
    log "Container '$CONTAINER' is not running (status: $STATE)."
    exit 1
fi

HEALTH=$(docker inspect --format '{{.State.Health.Status}}' "$CONTAINER" 2>/dev/null)
if [ -z "$HEALTH" ] || [ "$HEALTH" = "<no value>" ]; then
    HEALTH="none"
fi

if [ "$HEALTH" != "healthy" ]; then
    log "Container '$CONTAINER' is not healthy ($HEALTH)."
    exit 1
fi

docker exec "$CONTAINER" "$@"
RC=$?

if [ "$RC" -eq 0 ]; then
    # recovered: clear the stamp so the next failure alerts again
    rm -f "$STAMP"
    exit 0
fi

# failed: alert only if no stamp, or stamp is older than COOLDOWN
ALERT=1
if [ -f "$STAMP" ]; then
    AGE=$(( $(date +%s) - $(stat -c %Y "$STAMP" 2>/dev/null || "$(date +%s)") ))
    [ "$AGE" -lt "$COOLDOWN" ] && ALERT=0
fi

if [ "$ALERT" -eq 1 ]; then
    send_alert "$@"
fi

exit "$RC"
