#!/bin/sh

log() {
    echo "[$(date -u +%Y-%m-%dT%H:%M:%S+00:00)] $1" >&2
}

if [ $# -lt 2 ]; then
    log "Usage: $0 <container_name> <command>"
    exit 1
fi

CONTAINER="$1"
shift

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