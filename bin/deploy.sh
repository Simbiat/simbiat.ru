#!/usr/bin/env bash
#
# deploy.sh — expected location: ./bin/deploy.sh (one level below project root,
# so that "../.env" below resolves to the project root's .env file).
#
# Workflow:
#   0. If a pending-actions file exists from a previous incomplete run,
#      offer to resume it (skip everything else) or discard it.
#   1. Run the local Bun build scripts, one at a time.
#   2. Dry-run rsync against PROD, using ./config/deploy/rsync-filter.txt.
#   3. Classify changed paths into actions, using ./config/deploy/service-patterns.ini.
#   4. Show a summary. Ask for confirmation. Default: cancel.
#   5. Real rsync transfer.
#   6. Write the action list to the pending-actions file, then run each
#      action one at a time over its own SSH call, removing it from the
#      file only once it succeeds.
#   7. Log everything to a timestamped file.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# ---------------------------------------------------------------------------
# .env — RSYNC_REMOTE_HOST, RSYNC_REMOTE_USER, RSYNC_DEV_DIR, RSYNC_PROD_DIR
# ---------------------------------------------------------------------------

set -a; source "${SCRIPT_DIR}/../.env"; set +a

: "${RSYNC_REMOTE_HOST:?RSYNC_REMOTE_HOST not set in .env}"
: "${RSYNC_REMOTE_USER:?RSYNC_REMOTE_USER not set in .env}"
: "${RSYNC_DEV_DIR:?RSYNC_DEV_DIR not set in .env}"
: "${RSYNC_PROD_DIR:?RSYNC_PROD_DIR not set in .env}"

PROD_HOST="${RSYNC_REMOTE_USER}@${RSYNC_REMOTE_HOST}"
PROD_PATH="${RSYNC_PROD_DIR}"
LOCAL_PROJECT_ROOT="${RSYNC_DEV_DIR}"

# All relative paths below ("./compose.yaml", etc.) depend on this.
cd "$LOCAL_PROJECT_ROOT"

# ---------------------------------------------------------------------------
# CONFIG — fill in for this project
# ---------------------------------------------------------------------------

RSYNC_FILTER_FILE="${LOCAL_PROJECT_ROOT}/config/deploy/rsync-filter.txt"
SERVICE_PATTERNS_FILE="${LOCAL_PROJECT_ROOT}/config/deploy/service-patterns.ini"

LOG_DIR="${LOCAL_PROJECT_ROOT}/var/log"
TIMESTAMP="$(date +%Y-%m-%d_%H-%M-%S)"
LOG_FILE="${LOG_DIR}/deploy-${TIMESTAMP}.log"
PENDING_FILE="${LOG_DIR}/deploy-pending.txt"

SVC_FRANKENPHP="frankenphp"
BUN_SERVICE="bun"

# The 5 Bun build scripts, run in this order. Each entry is split on
# whitespace into separate argv elements before being passed to
# "docker compose run" — do not quote these when expanding below.
BUN_BUILD_COMMANDS=(
    "bun run generate:config"
    "bun run generate:css"
    "bun run generate:caddy"
    "bun run generate:mime"
    "bun run bundle"
)

MAINTENANCE_FLAG="/var/log/db_maintenance.flag"
#TODO: replace with endpoint for worker mode reset, when migrating to worker mode
OPCACHE_RESET_URL="http://localhost:2027/"

PATTERN_COMPOSER='(^|/)composer\.(json|lock)$'
# Changes to these files need to trigger Symfony rebuild.
PATTERN_ENV_CONFIG='(^|/)\.env(\..+)?$|(^|/)config/(packages|routes)/|(^|/)config/(services|bundles|routes)\.ya?ml$|(^|/)config/(bundles|preload)\.php$'
PATTERN_PHP='\.php$'
PATTERN_ROOT_COMPOSE='^compose\.ya?ml$'

# ---------------------------------------------------------------------------
# SETUP
# ---------------------------------------------------------------------------

mkdir -p "$LOG_DIR"
exec > >(tee -a "$LOG_FILE") 2>&1

log()  { echo "[$(date +%H:%M:%S)] $*"; }
fail() { log "ERROR: $*"; exit 1; }

[ -f "$RSYNC_FILTER_FILE" ]      || fail "Filter file not found: $RSYNC_FILTER_FILE"
[ -f "$SERVICE_PATTERNS_FILE" ]  || fail "Service pattern file not found: $SERVICE_PATTERNS_FILE"

# ---------------------------------------------------------------------------
# STEP 0 — RESUME CHECK
# ---------------------------------------------------------------------------

run_remote_action() {
    local cmd="$1"
    log "Remote: $cmd"
    #PROD_PATH and cmd are fully resolved on DEV before being sent; nothing here is meant to expand on PROD's side.
    # shellcheck disable=SC2029
    ssh "$PROD_HOST" "cd ${PROD_PATH} && ${cmd}"
}

execute_pending() {
    log "Resuming pending actions..."
    while [ -s "$PENDING_FILE" ]; do
        local next_cmd
        next_cmd="$(head -n 1 "$PENDING_FILE")"
        if run_remote_action "$next_cmd"; then
            tail -n +2 "$PENDING_FILE" > "${PENDING_FILE}.tmp"
            mv "${PENDING_FILE}.tmp" "$PENDING_FILE"
        else
            fail "Remote action failed: ${next_cmd}
Remaining actions are still listed in ${PENDING_FILE}.
Fix the problem on PROD if needed, then re-run this script to resume."
        fi
    done
    rm -f "$PENDING_FILE"
    log "All pending actions complete."
}

if [ -s "$PENDING_FILE" ]; then
    echo ""
    echo "===== UNFINISHED DEPLOY FOUND ====="
    echo "The following actions were not completed on a previous run:"
    cat "$PENDING_FILE"
    echo "===================================="
    read -r -p "Resume these actions now? [Y/n]: " RESUME
    RESUME="${RESUME:-Y}"
    if [[ "$RESUME" =~ ^[Yy]$ ]]; then
        execute_pending
        log "Deploy complete (resumed)."
        exit 0
    fi
    read -r -p "Discard pending actions instead? This is not reversible. [y/N]: " DISCARD
    DISCARD="${DISCARD:-N}"
    if [[ "$DISCARD" =~ ^[Yy]$ ]]; then
        rm -f "$PENDING_FILE"
        log "Pending actions discarded. Continuing with a fresh run."
    else
        log "Nothing resumed, nothing discarded. Exiting without changes."
        exit 0
    fi
fi

# ---------------------------------------------------------------------------
# STEP 1 — LOCAL BUN BUILD (runs first, so output is part of the sync)
# ---------------------------------------------------------------------------

log "Running Bun build scripts..."
for bun_cmd in "${BUN_BUILD_COMMANDS[@]}"; do
    log "Bun: $bun_cmd"
    # Split on whitespace into separate argv elements — do not quote
    # "$bun_cmd" when passing it on, or Compose receives one argument
    # instead of several and fails to find the binary.
    read -ra bun_argv <<< "$bun_cmd"
    if ! docker compose -f ./compose.yaml run --rm "$BUN_SERVICE" "${bun_argv[@]}"; then
        read -r -p "Bun script '${bun_cmd}' failed. Continue with remaining Bun scripts and the deploy? [y/N]: " CONT
        CONT="${CONT:-N}"
        [[ "$CONT" =~ ^[Yy]$ ]] || fail "Stopped after Bun build failure."
    fi
done

# ---------------------------------------------------------------------------
# STEP 2 — DRY RUN
# ---------------------------------------------------------------------------

log "Running rsync dry run..."
# DO *****NOT***** use `--delete-excluded` or it can result in data loss!
DRY_RUN_OUTPUT="$(rsync -a --delete-delayed --delay-updates --dry-run --itemize-changes \
    --filter="merge ${RSYNC_FILTER_FILE}" \
    "${LOCAL_PROJECT_ROOT}/" "${PROD_HOST}:${PROD_PATH}/")"

if [ -z "$DRY_RUN_OUTPUT" ]; then
    log "No changes detected. Nothing to deploy."
    exit 0
fi

log "Dry run complete. Changed items:"
echo "$DRY_RUN_OUTPUT"

CHANGED_PATHS="$(echo "$DRY_RUN_OUTPUT" | sed -E 's/^[^ ]+ //')"

# ---------------------------------------------------------------------------
# STEP 3 — LOAD SERVICE PATTERNS AND CLASSIFY
# ---------------------------------------------------------------------------

declare -a REBUILD_RULES=()   # "service|pattern"
declare -a RESTART_RULES=()
declare -A ALL_SERVICES_SEEN=()

trim() { echo "$1" | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//'; }

current_section=""
while IFS= read -r line; do
    line="${line%%;*}"
    line="$(trim "$line")"
    [ -z "$line" ] && continue
    [[ "$line" == \#* ]] && continue
    if [[ "$line" =~ ^\[(.+)\]$ ]]; then
        current_section="${BASH_REMATCH[1]}"
        ALL_SERVICES_SEEN["$current_section"]=1
        continue
    fi
    if [[ "$line" =~ ^rebuild[[:space:]]*=[[:space:]]*(.+)$ ]]; then
        IFS=',' read -ra pats <<< "${BASH_REMATCH[1]}"
        for p in "${pats[@]}"; do
            p="$(trim "$p")"
            [ -n "$p" ] && REBUILD_RULES+=("${current_section}|${p}")
        done
    elif [[ "$line" =~ ^restart[[:space:]]*=[[:space:]]*(.+)$ ]]; then
        IFS=',' read -ra pats <<< "${BASH_REMATCH[1]}"
        for p in "${pats[@]}"; do
            p="$(trim "$p")"
            [ -n "$p" ] && RESTART_RULES+=("${current_section}|${p}")
        done
    fi
done < "$SERVICE_PATTERNS_FILE"

need_composer=false
need_cache=false
need_opcache=false
root_compose_changed=false
declare -A need_rebuild=()
declare -A need_restart=()

while IFS= read -r path; do
    [ -z "$path" ] && continue

    [[ "$path" =~ $PATTERN_COMPOSER ]] && need_composer=true
    if [[ "$path" =~ $PATTERN_ENV_CONFIG ]] || [[ "$path" =~ $PATTERN_PHP ]] || [[ "$path" =~ $PATTERN_COMPOSER ]]; then
        need_cache=true
    fi
    [[ "$path" =~ $PATTERN_PHP ]] && need_opcache=true
    [[ "$path" =~ $PATTERN_ROOT_COMPOSE ]] && root_compose_changed=true

    for rule in "${REBUILD_RULES[@]}"; do
        svc="${rule%%|*}"; pattern="${rule#*|}"
        [[ "$path" == *"$pattern"* ]] && need_rebuild["$svc"]=1
    done
    for rule in "${RESTART_RULES[@]}"; do
        svc="${rule%%|*}"; pattern="${rule#*|}"
        [[ "$path" == *"$pattern"* ]] && need_restart["$svc"]=1
    done
done <<< "$CHANGED_PATHS"

# A service already flagged for rebuild does not also need a separate
# restart-only entry — it gets recreated either way, see STEP 4.
for svc in "${!need_rebuild[@]}"; do
    unset "need_restart[$svc]" 2>/dev/null || true
done

if $root_compose_changed; then
    echo ""
    echo "compose.yaml at the project root changed. This file can affect"
    echo "networks, volumes, and other settings shared across services."
    echo "This script cannot tell which services, if any, need a restart."
    echo "Known services: ${!ALL_SERVICES_SEEN[*]}"
    read -r -p "Restart ALL known services now? [y/N] (No = you restart manually later): " RESTART_ALL
    RESTART_ALL="${RESTART_ALL:-N}"
    if [[ "$RESTART_ALL" =~ ^[Yy]$ ]]; then
        for svc in "${!ALL_SERVICES_SEEN[@]}"; do
            [ -n "${need_rebuild[$svc]+x}" ] || need_restart["$svc"]=1
        done
    fi
fi

# ---------------------------------------------------------------------------
# STEP 4 — BUILD ACTION LIST + SUMMARY + CONFIRMATION
# ---------------------------------------------------------------------------

declare -a rebuild_svc_list=()
for svc in "${!need_rebuild[@]}"; do rebuild_svc_list+=("$svc"); done

declare -a restart_svc_list=()
for svc in "${!need_restart[@]}"; do restart_svc_list+=("$svc"); done

# Rebuild-needing services AND restart-only services are recreated
# together in one combined "up -d" call later, so Compose resolves
# cross-service startup order via depends_on for the whole affected
# set at once — this script does not hand-roll that ordering itself.
declare -a recreate_svc_list=("${rebuild_svc_list[@]}" "${restart_svc_list[@]}")

frankenphp_recreated=false
for svc in "${recreate_svc_list[@]}"; do
    [ "$svc" == "$SVC_FRANKENPHP" ] && frankenphp_recreated=true
done

declare -a ACTIONS=()

# Build first, before the maintenance flag goes up. This doesn't touch
# the live containers or the shared project volume, so there's no
# reason for it to sit inside the maintenance window.
if [ "${#rebuild_svc_list[@]}" -gt 0 ]; then
    ACTIONS+=("docker compose -f ./compose.yaml build ${rebuild_svc_list[*]}")
fi

if $need_composer || $need_cache; then
    ACTIONS+=("touch ${MAINTENANCE_FLAG}")
fi
if $need_composer; then
    # Uses the image built just above if frankenphp was rebuilt this
    # deploy; otherwise the currently-live image, unchanged.
    ACTIONS+=("docker compose -f ./compose.yaml run --rm ${SVC_FRANKENPHP} composer install --no-dev --optimize-autoloader")
fi
if $need_cache; then
    ACTIONS+=("docker compose -f ./compose.yaml run --rm ${SVC_FRANKENPHP} bin/console cache:clear --env=prod --no-debug")
    ACTIONS+=("docker compose -f ./compose.yaml run --rm ${SVC_FRANKENPHP} bin/console dotenv:dump prod")
fi

# Recreate phase — see the comment on recreate_svc_list above. Restart-
# only services get a full recreate here too, not a lighter "restart":
# safe as long as their real state lives in a volume outside the
# container, true for everything in service-patterns.ini today.
if [ "${#recreate_svc_list[@]}" -gt 0 ]; then
    ACTIONS+=("docker compose -f ./compose.yaml up -d --force-recreate ${recreate_svc_list[*]}")
fi

if $need_composer || $need_cache; then
    ACTIONS+=("rm -f ${MAINTENANCE_FLAG}")
fi

# Skip if frankenphp itself was rebuilt/recreated above — a fresh
# process already starts with an empty opcache, and hitting the
# endpoint right after recreation is redundant at best and can race a
# container that isn't fully ready yet.
if $need_opcache && ! $frankenphp_recreated; then
    ACTIONS+=("docker compose -f ./compose.yaml exec ${SVC_FRANKENPHP} curl -fsS ${OPCACHE_RESET_URL}")
fi

echo ""
echo "===== DEPLOY SUMMARY ====="
echo "- Sync files to PROD (rsync, --delete-delayed --delay-updates)"
if [ "${#ACTIONS[@]}" -eq 0 ]; then
    echo "- No PROD-side actions required"
else
    for a in "${ACTIONS[@]}"; do
        echo "- $a"
    done
fi
echo "==========================="
echo ""

read -r -p "Proceed with deploy? [y/N]: " CONFIRM
CONFIRM="${CONFIRM:-N}"
if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
    log "Deploy cancelled by user."
    exit 0
fi

# ---------------------------------------------------------------------------
# STEP 5 — REAL RSYNC
# ---------------------------------------------------------------------------

log "Running real rsync transfer..."
rsync -a --delete-delayed --delay-updates --itemize-changes \
    --filter="merge ${RSYNC_FILTER_FILE}" \
    "${LOCAL_PROJECT_ROOT}/" "${PROD_HOST}:${PROD_PATH}/" \
    || fail "rsync transfer failed. No PROD-side actions were attempted."

# ---------------------------------------------------------------------------
# STEP 6 — WRITE PENDING ACTIONS, THEN RUN ONE BY ONE
# ---------------------------------------------------------------------------

if [ "${#ACTIONS[@]}" -gt 0 ]; then
    printf '%s\n' "${ACTIONS[@]}" > "$PENDING_FILE"
    execute_pending
else
    log "No PROD-side actions required. Sync only."
fi

log "Deploy complete."
