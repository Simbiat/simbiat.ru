#!/bin/bash
# cron-wrapper.sh - run a command inside a container with alerting on failure
set -Eeuo pipefail

STATE_DIR="/var/log/cron"
COOLDOWN=3600                       # seconds between repeated alerts for the same signature
MAINTENANCE_CONTAINERS="frankenphp mariadb"   # containers for which the flag is meaningful
MAINTENANCE_FLAG="/var/log/db_maintenance.flag"
MAILER="/etc/supercronic/bin/send-mail.sh"
POSTFIX_CONTAINER="postfix"         # container delivering the alert mail
HEALTH_GRACE=120                    # seconds to wait for healthcheck after container start
ALERT_LOCK_WAIT=120                 # seconds to wait for a sibling holding the alert lock
MAX_OUTPUT_LINES=50                 # max lines of exec output captured into alerts/stamps

WRAPPER_ARGS="$*"                   # captured early, safe to reference in on_error

log() {
    echo "time=\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\" msg=\"$1\"" >&2
}

# Known bug https://github.com/koalaman/shellcheck/issues/2542
# shellcheck disable=SC2317
on_error() {
    local rc=$?
    trap - ERR
    log "INTERNAL ERROR rc=$rc at line $1, command: ${BASH_COMMAND:-?}"
    "$MAILER" "[Alert] Cron wrapper crashed" \
        "cron-wrapper for '${CONTAINER:-unknown}' (args: ${WRAPPER_ARGS:-none}) failed internally at line $1, rc=$rc. Command: ${BASH_COMMAND:-?}" 2 \
        || log "CRITICAL: alert mailer itself failed, no email sent"
    exit "$rc"
}
trap 'on_error $LINENO' ERR

mkdir -p "$STATE_DIR"

find "$STATE_DIR" -type f -name '*.lock' -mtime +7 -delete

# Temp files for capturing exec output; cleaned up on any exit path.
TMP_STDOUT=$(mktemp "$STATE_DIR/.cron.out.XXXXXX") || exit 1
TMP_STDERR=$(mktemp "$STATE_DIR/.cron.err.XXXXXX") || { rm -f -- "$TMP_STDOUT"; exit 1; }

# Known bug https://github.com/koalaman/shellcheck/issues/2542
# shellcheck disable=SC2317
cleanup() {
    rm -f -- "$TMP_STDOUT" "$TMP_STDERR";
}
trap cleanup EXIT

# Remove a stamp only if no sibling is concurrently alerting on it (holding the
# flock on the .lock inode). Leaving the .lock file itself in place is correct:
# deleting a lock file splits the lock into two inodes and breaks deduplication.
clear_stamp() {
    (
        flock -n 9 2>/dev/null || exit 0   # busy: sibling is alerting, leave its stamp alone
        rm -f -- "$1"
    ) 9>"$1.lock"
}

# Send at most one alert per COOLDOWN window, serialized against concurrent
# instances of the same signature. Args: stamp, subject, body.
alert_once() {
    local stamp="$1" subject="$2" body="$3"

    # Maintenance gate: only meaningful for the DB/web containers
    if [ -f "$MAINTENANCE_FLAG" ]; then
        for c in $MAINTENANCE_CONTAINERS; do
            if [ "${CONTAINER:-}" = "$c" ]; then
                log "Maintenance flag present, alert suppressed: $body"
                return 0
            fi
        done
    fi

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
}

if [ $# -lt 2 ]; then
    log "Usage: $0 <container_name> <command>"
    exit 1
fi

CONTAINER="$1"
shift

# Pre-flight: shared stamps (not per-job) so a global outage emails once per
# cooldown across ALL cron jobs, not once per job signature.
DOCKER_STAMP="$STATE_DIR/cron_docker"
POSTFIX_STAMP="$STATE_DIR/cron_postfix"

# unique signature per container + command combination
CMD_SIG=$(printf '%s|%s' "$CONTAINER" "$*" | md5sum | cut -c1-12)
CTR_SIG=$(printf '%s' "$CONTAINER" | md5sum | cut -c1-12)
CMD_STAMP="$STATE_DIR/cron_fail-$CMD_SIG"
CTR_STAMP="$STATE_DIR/cron_container-$CTR_SIG"

# 1. Docker socket sanity check. Without it every job produces its own
#    "cannot connect" email; the shared stamp collapses those into one.
if ! docker ps >/dev/null 2>&1; then
    log "Docker is not available (socket unreachable or daemon down)."
    touch "$DOCKER_STAMP"   # marker only, no alert on purpose
    exit 1
fi
clear_stamp "$DOCKER_STAMP"

# 2. If postfix is down, alerts cannot be delivered - exit quietly, leaving a
#    marker for post-mortem. Cleared as soon as postfix is healthy again.
POSTFIX_HEALTH=$(docker inspect --format '{{.State.Health.Status}}' "$POSTFIX_CONTAINER" 2>/dev/null || true)
if [ -z "$POSTFIX_HEALTH" ] || [ "$POSTFIX_HEALTH" = "<no value>" ]; then
    POSTFIX_HEALTH="none"
fi
if [ "$POSTFIX_HEALTH" != "healthy" ] && [ "$POSTFIX_HEALTH" != "none" ]; then
    log "Postfix container '$POSTFIX_CONTAINER' not healthy ($POSTFIX_HEALTH); skipping (alerts cannot be delivered)."
    touch "$POSTFIX_STAMP"   # marker only, no alert on purpose
    exit 1
fi
clear_stamp "$POSTFIX_STAMP"

STATE=$(docker inspect --format '{{.State.Status}}' "$CONTAINER" 2>/dev/null || true)

if [ -z "$STATE" ]; then
    log "Container '$CONTAINER' not found."
    alert_once "$CTR_STAMP" "[Alert] Missing container" \
        "Container '$CONTAINER' (expected by cron job: $*) not found on the host."
    exit 1
fi

if [ "$STATE" != "running" ]; then
    log "Container '$CONTAINER' is not running (status: $STATE)."
    alert_once "$CTR_STAMP" "[Alert] Container not running" \
        "Container '$CONTAINER' is not running (status: $STATE)."
    exit 1
fi

HEALTH=$(docker inspect --format '{{.State.Health.Status}}' "$CONTAINER" 2>/dev/null || true)
if [ -z "$HEALTH" ] || [ "$HEALTH" = "<no value>" ]; then
    HEALTH="none"
fi

if [ "$HEALTH" != "healthy" ]; then
    # Grace period: give a just-started container time to finish its healthcheck.
    STARTED_AT=$(docker inspect --format '{{.State.StartedAt}}' "$CONTAINER" 2>/dev/null || true)
    if [ -n "$STARTED_AT" ]; then
        CONTAINER_AGE=$(( $(date +%s) - $(date -d "$STARTED_AT" +%s) ))
        if [ "$CONTAINER_AGE" -lt "$HEALTH_GRACE" ]; then
            log "Container '$CONTAINER' not healthy ($HEALTH) but started ${CONTAINER_AGE}s ago (< ${HEALTH_GRACE}s), staying quiet."
            exit 1
        fi
    fi
    log "Container '$CONTAINER' is not healthy ($HEALTH)."
    alert_once "$CTR_STAMP" "[Alert] Container not healthy" \
        "Container '$CONTAINER' is not healthy (status: $HEALTH); command not executed."
    exit 1
fi

# Container confirmed running + healthy: any outstanding container-level alert
# is resolved, so retire it (and give the next failure a fresh cooldown).
clear_stamp "$CTR_STAMP"

# Run the command, capturing stdout and stderr separately, then replay both so
# Supercronic's own log capture is untouched.
RC=0
docker exec "$CONTAINER" "$@" >"$TMP_STDOUT" 2>"$TMP_STDERR" || RC=$?

# Replay the original streams so Supercronic logs them as usual:
cat "$TMP_STDOUT"
cat "$TMP_STDERR" >&2

if [ "$RC" -eq 0 ]; then
    clear_stamp "$CMD_STAMP"
    exit 0
fi

# Build a snippet of the failing run's output for the alert body / stamp.
SNIPPET=""
[ -s "$TMP_STDOUT" ] && SNIPPET="--- captured stdout (last $MAX_OUTPUT_LINES lines) ---
$(tail -n "$MAX_OUTPUT_LINES" "$TMP_STDOUT")"
[ -s "$TMP_STDERR" ] && SNIPPET="${SNIPPET:+$SNIPPET
}--- captured stderr (last $MAX_OUTPUT_LINES lines) ---
$(tail -n "$MAX_OUTPUT_LINES" "$TMP_STDERR")"

alert_once "$CMD_STAMP" "[Alert] Failed cron job" \
    "Cron job \`$CONTAINER: $*\` failed with exit code $RC.${SNIPPET:+
}$SNIPPET"

exit "$RC"
