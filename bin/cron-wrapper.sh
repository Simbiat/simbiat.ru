#!/bin/bash
# cron-wrapper.sh - run a command inside a container with alerting on failure
set -Eeuo pipefail

WRAPPER_ARGS="$*"                   # captured early, safe to reference in on_error
STATE_DIR="/var/log/cron"
INTERNAL_STAMP="cron_wrapper_internal"
ALERT_ONCE="/etc/supercronic/bin/alert-once.sh"
CLEAR_STAMP="/etc/supercronic/bin/clear-stamp.sh"
CHECK_CONTAINER="/etc/supercronic/bin/check-container.sh"

log() {
    echo "time=\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\" msg=\"$1\"" >&2
}

# Known bug https://github.com/koalaman/shellcheck/issues/2542
# shellcheck disable=SC2317
on_error() {
    local rc=$?
    trap - ERR
    log "INTERNAL ERROR rc=$rc at line $1, command: ${BASH_COMMAND:-?}"
    "$ALERT_ONCE" "$INTERNAL_STAMP" "[Alert] Cron wrapper crashed" \
            "cron-wrapper for '${CONTAINER:-unknown}' (args: ${WRAPPER_ARGS:-none}) failed internally at line $1, rc=$rc. Command: ${BASH_COMMAND:-?}"
    exit "$rc"
}
trap 'on_error $LINENO' ERR

mkdir -p "$STATE_DIR"

find "$STATE_DIR" -type f -name '*' -mtime +7 -delete

# Temp files for capturing exec output; cleaned up on any exit path.
TMP_STDOUT=$(mktemp "$STATE_DIR/.cron.out.XXXXXX") || exit 1
TMP_STDERR=$(mktemp "$STATE_DIR/.cron.err.XXXXXX") || { rm -f -- "$TMP_STDOUT"; exit 1; }

# Known bug https://github.com/koalaman/shellcheck/issues/2542
# shellcheck disable=SC2317
cleanup() {
    rm -f -- "$TMP_STDOUT" "$TMP_STDERR";
}
trap cleanup EXIT

if [ $# -lt 2 ]; then
    log "Usage: $0 <container_name> <command>"
    exit 1
fi

CONTAINER="$1"
shift

# unique signature per container + command combination
CMD_SIG=$(printf '%s' "$*" | md5sum | cut -c1-12)
CMD_STAMP="cron_fail-$CONTAINER-$CMD_SIG"

RC=0
"$CHECK_CONTAINER" "$CONTAINER" || RC=$?
if [ "$RC" -ne 0 ]; then
    exit "$RC"
fi

# Run the command, capturing stdout and stderr separately, then replay both so Supercronic's own log capture is untouched.
RC=0
docker exec "$CONTAINER" "$@" >"$TMP_STDOUT" 2>"$TMP_STDERR" || RC=$?

# Replay the original streams so Supercronic logs them as usual:
cat "$TMP_STDOUT"
cat "$TMP_STDERR" >&2

if [ "$RC" -eq 0 ]; then
    "$CLEAR_STAMP" "$CMD_STAMP"
    "$CLEAR_STAMP" "$INTERNAL_STAMP"
    exit 0
fi

# Embed the failing run's output for the alert body / stamp.
SNIPPET=""
[ -s "$TMP_STDOUT" ] && SNIPPET="--- captured stdout ---
$(cat "$TMP_STDOUT")"
[ -s "$TMP_STDERR" ] && SNIPPET="${SNIPPET:+$SNIPPET
}--- captured stderr ---
$(cat "$TMP_STDERR")"

"$ALERT_ONCE" "$CMD_STAMP" "[Alert] Failed cron job" \
    "Cron job \`$CONTAINER: $*\` failed with exit code $RC.${SNIPPET:+
}$SNIPPET" || log "Failed to send alert"

exit "$RC"
