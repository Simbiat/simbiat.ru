#!/bin/bash
# check-container.sh - check if container is running and healthy
#
# Usage: check-container.sh <container_name>

set -Eeuo pipefail

log() {
    echo "time=\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\" msg=\"$1\"" >&2
}

CLEAR_STAMP="/etc/supercronic/bin/clear-stamp.sh"
ALERT_ONCE="/etc/supercronic/bin/alert-once.sh"
DOCKER_STAMP="docker"
HEALTH_GRACE=120                    # seconds to wait for healthcheck after container start
ALERT_STATE_DIR="/var/log/cron"
MAINTENANCE_FLAG="/var/log/db_maintenance.flag"
MAINTENANCE_CONTAINERS="frankenphp mariadb"   # containers for which the flag is meaningful

CONTAINER="$1"
CTR_STAMP="container-$CONTAINER"

# Docker socket sanity check. Without it every job produces its own "cannot connect" email; the shared stamp collapses those into one.
if ! docker ps >/dev/null 2>&1; then
    log "Docker is not available (socket unreachable or daemon down)."
    touch "$ALERT_STATE_DIR/$DOCKER_STAMP"   # marker only, no alert since can't send it
    exit 12
fi
"$CLEAR_STAMP" "$DOCKER_STAMP"

STATE=$(docker inspect --format '{{.State.Status}}' "$CONTAINER" 2>/dev/null || true)

if [ -z "$STATE" ]; then
    log "Container '$CONTAINER' not found."
    if [ "$CONTAINER" != "postfix" ]; then
        "$ALERT_ONCE" "$CTR_STAMP" "[Alert] Missing container" \
            "Container '$CONTAINER' not found on the host." || true
    else
        touch "$ALERT_STATE_DIR/$CTR_STAMP"
    fi
    exit 11
fi

# Maintenance gate
if [ -f "$MAINTENANCE_FLAG" ]; then
    for c in $MAINTENANCE_CONTAINERS; do
        if [ "${CONTAINER:-}" = "$c" ]; then
            log "Maintenance flag detected, treating container as exited"
            exit 7
        fi
    done
fi

if [ "$STATE" != "running" ]; then
    log "Container '$CONTAINER' is not running (status: $STATE)."
    if [ "$CONTAINER" != "postfix" ]; then
        "$ALERT_ONCE" "$CTR_STAMP" "[Alert] Container not running" \
            "Container '$CONTAINER' is not running (status: $STATE)." || true
    else
        touch "$ALERT_STATE_DIR/$CTR_STAMP"
    fi
    case "$STATE" in
      restarting) exit 5  ;;
      paused)     exit 6  ;;
      exited)     exit 7  ;;
      created)    exit 8  ;;
      removing)   exit 9  ;;
      dead)       exit 10 ;;
      *)
        log "Unknown status $STATE"
        exit 13
        ;;
    esac
fi

HEALTH=$(docker inspect --format '{{.State.Health.Status}}' "$CONTAINER" 2>/dev/null || true)
if [ -z "$HEALTH" ] || [ "$HEALTH" = "<no value>" ]; then
    HEALTH="none"
fi

if [ "$HEALTH" != "healthy" ]; then
    # Grace period: give a just-started container time to finish its healthcheck.
    STARTED_AT=$(docker inspect --format '{{.State.StartedAt}}' "$CONTAINER" 2>/dev/null || true)
    if [ -n "$STARTED_AT" ]; then
        # BusyBox/Alpine do not like `T` and fractional seconds in the timestamp
        STARTED_AT_SEC=$(printf '%s' "$STARTED_AT" | sed 's/T/ /; s/\.[0-9]*Z$//')
        CONTAINER_AGE=$(( $(date +%s) - $(date -d "$STARTED_AT_SEC" +%s) ))
        if [ "$CONTAINER_AGE" -lt "$HEALTH_GRACE" ]; then
            log "Container '$CONTAINER' not healthy ($HEALTH) but started ${CONTAINER_AGE}s ago (< ${HEALTH_GRACE}s), staying quiet."
            exit 1
        fi
    fi
    log "Container '$CONTAINER' is not healthy ($HEALTH)."
    if [ "$CONTAINER" != "postfix" ]; then
        "$ALERT_ONCE" "$CTR_STAMP" "[Alert] Container not healthy" \
            "Container '$CONTAINER' is not healthy (status: $HEALTH)." || true
    else
        touch "$ALERT_STATE_DIR/$CTR_STAMP"
    fi
    case "$HEALTH" in
      unhealthy) exit 2  ;;
      none)     exit 3  ;;
      starting)     exit 4  ;;
      *)
        log "Unknown health state $HEALTH"
        exit 13
        ;;
    esac
fi

# Container confirmed running + healthy: any outstanding container-level alert
# is resolved, so retire it (and give the next failure a fresh cooldown).
"$CLEAR_STAMP" "$CTR_STAMP"

exit 0
