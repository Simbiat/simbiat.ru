#!/usr/bin/env bash
#
# Script to backup some data from PROD server and create an extra mirror of the project files from WSL.
#
# To trigger from Windows Task Scheduler via:
#   Program:   C:\Windows\System32\wsl.exe
#   Arguments: -d Ubuntu -u simbiat -- /home/simbiat/simbiat.eu/scripts/rsync-backup.sh
#
# Make executable once: chmod +x rsync-backup.sh

set -a; source "$(dirname "${BASH_SOURCE[0]}")/../.env"; set +a

set -uo pipefail   # no -e: we want failed jobs to be retried/logged, not to abort the whole script

# ---- Configuration -----------------------------------------------------
PROJECT_DIR="$RSYNC_DEV_DIR" # Main project folder
DB_BCK_DIR="$RSYNC_BACKUP_DIR"        # Folder for database backups
MIRROR_DIR="$RSYNC_MIRROR_DIR"
RSYNC_REMOTE="$RSYNC_REMOTE_USER@$RSYNC_REMOTE_HOST:$RSYNC_PROD_DIR"

DATE_STAMP="$(date +%Y.%m.%d)"
LOG_FILE="${MIRROR_DIR}/var/log/rsync-${DATE_STAMP}.log"

MAX_RETRIES=10
RETRY_DELAY=15   # seconds
OVERALL_FAILED=0
FAILED_STEPS=()

RSYNC_FLAGS=(--recursive --checksum --times --force --prune-empty-dirs
             --human-readable --progress --stats
             -e "ssh -o ServerAliveInterval=15 -o ServerAliveCountMax=3"
             --exclude=.gitignore --exclude=.git)

# ---- Helpers -------------------------------------------------------------
log() {
    printf '[%s] %s\n' "$(date '+%H:%M:%S')" "$*"
}

# sync_retry "description" cmd arg1 arg2 ...
sync_retry() {
    local desc="$1"; shift
    log "$desc"
    local attempt=0
    until "$@"; do
        local rc=$?
        attempt=$((attempt + 1))
        log "  Attempt ${attempt} failed with exit code ${rc}"
        if [ "$attempt" -ge "$MAX_RETRIES" ]; then
            log "  FAILED after ${MAX_RETRIES} attempts: ${desc}"
            OVERALL_FAILED=1
            FAILED_STEPS+=("$desc")
            return 1
        fi
        sleep "$RETRY_DELAY"
    done
    return 0
}

# Remove files older than X days
cleanup_older_than() {
    local dir="$1" pattern="$2" days="$3"
    [ -d "$dir" ] || return 0
    find "$dir" -maxdepth 1 -type f -name "$pattern" -mtime "+${days}" -print \
        | while IFS= read -r f; do
            log "Deleting ${f}"
            rm -f -- "$f"
        done
}

# ---- Main ------------------------------------------------------------
# Everything from here on prints to the console AND appends to the log file -
# no need to redirect each command individually anymore.
exec > >(tee -a "$LOG_FILE") 2>&1

log "=== Run started: $(date) ==="

# PHPStorm launches the PHP tools under root and leaves a lot of crap behind. Since script runs under non-root user need to user sudo here.
sudo find "$PROJECT_DIR/data/temp" -type d -regextype posix-extended -regex '.*/(PHP_CodeSniffertemp_|PHPStantemp_|Psalmtemp_)[^/]*' -exec rm -rf {} +

# Pulled straight into the mirror, not into $ROOT_DIR first - these are PROD's
# archived logs, not something the live WSL2 project tree needs to hold.
sync_retry "Syncing /var/log/" \
    rsync "${RSYNC_FLAGS[@]}" --remove-source-files \
    --exclude=php.log --exclude=caddy.log --exclude='caddy*.log.gz' \
    --exclude=bouncer.log --exclude='bouncer*.log.gz' --exclude='*.flag' \
    --exclude='crowdsec*.gz' --exclude=mariadb.log --exclude=cron.log \
    --exclude='access*.*' --exclude=crowdsec.log --exclude=crowdsec_api.log \
    --exclude=.crowdsec.log.swp --exclude=mail.log \
    "${RSYNC_REMOTE}/var/log/" "${MIRROR_DIR}/var/log/"

sync_retry "Syncing /build/DDL/" \
    rsync "${RSYNC_FLAGS[@]}" "${RSYNC_REMOTE}/build/DDL/" "${MIRROR_DIR}/build/DDL/"

sync_retry "Syncing /data/uploaded/" \
    rsync "${RSYNC_FLAGS[@]}" --delete "${RSYNC_REMOTE}/data/uploaded/" "${PROJECT_DIR}/data/uploaded/"

sync_retry "Syncing /var/sitemap/" \
    rsync "${RSYNC_FLAGS[@]}" --delete "${RSYNC_REMOTE}/var/sitemap/" "${MIRROR_DIR}/var/sitemap/"

sync_retry "Syncing /data/ffstatistics/" \
    rsync "${RSYNC_FLAGS[@]}" --delete "${RSYNC_REMOTE}/data/ffstatistics/" "${MIRROR_DIR}/data/ffstatistics/"

sync_retry "Syncing /data/uploadedimages/" \
    rsync "${RSYNC_FLAGS[@]}" --delete "${RSYNC_REMOTE}/data/uploadedimages/" "${PROJECT_DIR}/data/uploadedimages/"

sync_retry "Syncing /public/assets/images/fftracker/crests-components/" \
    rsync "${RSYNC_FLAGS[@]}" \
    "${RSYNC_REMOTE}/public/assets/images/fftracker/crests-components/" \
    "${PROJECT_DIR}/public/assets/images/fftracker/crests-components/"

sync_retry "Syncing /public/assets/images/fftracker/icons/" \
    rsync "${RSYNC_FLAGS[@]}" \
    "${RSYNC_REMOTE}/public/assets/images/fftracker/icons/" \
    "${PROJECT_DIR}/public/assets/images/fftracker/icons/"

mkdir -p "$DB_BCK_DIR"
(
    sync_retry "Syncing /data/backups/" \
        rsync "${RSYNC_FLAGS[@]}" --partial --remove-source-files \
        --include='*.7z' --exclude='*.sql' \
        "${RSYNC_REMOTE}/data/backups/" "${DB_BCK_DIR}/"
)

# ---- Mirror project dir --
sync_retry "Syncing project to mirror folder" \
    rsync --recursive --checksum --times --force --prune-empty-dirs \
            --human-readable --progress --stats --delete \
    --exclude=vendor/ --exclude=node_modules/ --exclude=var/ \
    --exclude=data/backups/ --exclude=data/ffstatistics/ \
    --exclude=var/mergedcrests/ --exclude=var/sitemap/ --exclude=data/temp/ \
    --include=data/backups/.gitignore --include=data/ffstatistics/.gitignore \
    --include=var/mergedcrests/.gitignore --include=var/log/.gitignore \
    --include=var/sitemap/.gitignore --include=logs/.gitignore \
    --include=data/temp/.gitignore --include=data/temp/mariadb/.gitignore \
    "${PROJECT_DIR}/" "${MIRROR_DIR}/"

cleanup_older_than "$DB_BCK_DIR" "*.sql"          1
cleanup_older_than "$DB_BCK_DIR" "*-monthly.7z"  84
cleanup_older_than "$DB_BCK_DIR" "*-weekly.7z"   42
cleanup_older_than "$DB_BCK_DIR" "*-daily.7z"    14
cleanup_older_than "$DB_BCK_DIR" "*-users.7z"    14
cleanup_older_than "$DB_BCK_DIR" "*-physical.7z"  7
cleanup_older_than "${MIRROR_DIR}/var/log" "*.log" 14

if [ "$OVERALL_FAILED" -eq 1 ]; then
    log "=== Completed WITH FAILURES ==="
    for step in "${FAILED_STEPS[@]}"; do
        log "  - ${step}"
    done
    exit 1
else
    log "=== Completed ==="
    exit 0
fi
