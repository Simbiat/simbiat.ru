#!/usr/bin/env bash
#
# deploy.sh - expected location: ./bin/deploy.sh (one level below project root,
# so that "../.env" below resolves to the project root's .env file).
#
# Workflow:
#   1. Log everything to a timestamped file.
#   2. If a pending-actions file exists from a previous incomplete run,
#      offer to resume it (skip everything else) or discard it.
#   3. Run Bun build scripts, generate files with Composer and Symfony for PROD
#   4. Dry-run rsync against PROD, using ./config/deploy/rsync-jobs.ini and
#      ./config/deploy/rsync-filter.txt.
#   5. Classify changed paths into actions, using ./config/deploy/service-patterns.ini.
#   6. Show a summary, with the exact commands that will run. Ask for
#      confirmation. Default: cancel.
#   7. Set the maintenance flag, then the real rsync transfers.
#   8. Write the action list (ending in removing the maintenance flag)
#      to the pending-actions file, then run each action one at a time
#      over its own SSH call, removing it from the file once it succeeds.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# ---------------------------------------------------------------------------
# .env - RSYNC_REMOTE_HOST, RSYNC_REMOTE_USER, RSYNC_DEV_DIR, RSYNC_PROD_DIR
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
# CONFIG - fill in for this project
# ---------------------------------------------------------------------------

RSYNC_FILTER_FILE="${LOCAL_PROJECT_ROOT}/config/deploy/rsync-filter.txt"
SERVICE_PATTERNS_FILE="${LOCAL_PROJECT_ROOT}/config/deploy/service-patterns.ini"
RSYNC_JOBS_FILE="${LOCAL_PROJECT_ROOT}/config/deploy/rsync-jobs.ini"

LOG_DIR="${LOCAL_PROJECT_ROOT}/var/log"
TIMESTAMP="$(date +%Y-%m-%d_%H-%M-%S)"
LOG_FILE="${LOG_DIR}/deploy-${TIMESTAMP}.log"
PENDING_FILE="${LOG_DIR}/deploy-pending.txt"

SVC_FRANKENPHP="frankenphp"

# Isolated build output for PROD-targeted vendor/cache/importmap/env -
# never mixed with DEV's own working copies of the same. Must be hidden
# from the main project rsync (rsync-filter.txt: "H ./var/deploy/"),
# since it's transferred separately below with its own source/destination
# pairs, not via the main filter-driven sync.
DEPLOY_ARTIFACTS_DIR="${LOCAL_PROJECT_ROOT}/var/deploy"

# Maintenance flag is seen by FrankenPHP and results in custom error pages
# This is relative to PROD directory, not the system's `/var/log`, so `.` is important.
MAINTENANCE_FLAG="./var/log/db_maintenance.flag"

#TODO: replace with endpoint for worker mode reset, when migrating to worker mode
# URL to call inside container to reset opcache
OPCACHE_RESET_URL="http://localhost:2027/"

PATTERN_ROOT_COMPOSE='^compose\.ya?ml$'

RSYNC_COMMON_FLAGS=(--recursive --links --perms --executability --group --owner --devices --specials --delete-delay --delay-updates --checksum --omit-dir-times --itemize-changes)

# ---------------------------------------------------------------------------
# SYNC JOBS - loaded from ./config/deploy/rsync-jobs.ini (see that file
# for the format). build_rsync_command() is the single place that
# assembles a runnable command from these - used for the dry run, the
# confirmation summary, and the real transfer, so all three always agree
# on exactly what will run.
# ---------------------------------------------------------------------------

declare -a SYNC_LABELS=()
declare -A SYNC_SOURCES=()
declare -A SYNC_DESTS=()
declare -A SYNC_FILTERS=()

trim() { echo "$1" | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//'; }

load_sync_jobs() {
    local current_section=""
    local line
    while IFS= read -r line; do
        line="${line%%;*}"
        line="$(trim "$line")"
        [ -z "$line" ] && continue
        [[ "$line" == \#* ]] && continue
        if [[ "$line" =~ ^\[(.+)\]$ ]]; then
            current_section="${BASH_REMATCH[1]}"
            SYNC_LABELS+=("$current_section")
            SYNC_SOURCES["$current_section"]=""
            SYNC_DESTS["$current_section"]=""
            SYNC_FILTERS["$current_section"]="false"
            continue
        fi
        if [[ "$line" =~ ^source[[:space:]]*=[[:space:]]*(.*)$ ]]; then
            SYNC_SOURCES["$current_section"]="$(trim "${BASH_REMATCH[1]}")"
        elif [[ "$line" =~ ^destination[[:space:]]*=[[:space:]]*(.*)$ ]]; then
            SYNC_DESTS["$current_section"]="$(trim "${BASH_REMATCH[1]}")"
        elif [[ "$line" =~ ^filter[[:space:]]*=[[:space:]]*(.*)$ ]]; then
            SYNC_FILTERS["$current_section"]="$(trim "${BASH_REMATCH[1]}")"
        fi
    done < "$RSYNC_JOBS_FILE"
}

# Builds one rsync command as a single string. $1 = job label (a section
# name from rsync-jobs.ini). $2 = extra flags, e.g. "--dry-run", or "" for
# a real run.
build_rsync_command() {
    local label="$1"
    local extra_flags="$2"
    local src="${LOCAL_PROJECT_ROOT}/${SYNC_SOURCES[$label]}"
    local dst="${PROD_PATH}/${SYNC_DESTS[$label]}"
    local cmd="rsync ${RSYNC_COMMON_FLAGS[*]}"
    [ -n "$extra_flags" ] && cmd="${cmd} ${extra_flags}"
    [ "${SYNC_FILTERS[$label]}" == "true" ] && cmd="${cmd} --filter=\"merge ${RSYNC_FILTER_FILE}\""
    cmd="${cmd} \"${src}\" \"${PROD_HOST}:${dst}\""
    echo "$cmd"
}

# ---------------------------------------------------------------------------
# SETUP
# ---------------------------------------------------------------------------

# Only LOG_DIR (needed for the log redirection just below) and the bare
# DEPLOY_ARTIFACTS_DIR (needed so the "touch" calls in STEP 1 don't fail
# on a missing parent, on a genuinely first-ever run) are pre-created.
mkdir -p "$LOG_DIR" "$DEPLOY_ARTIFACTS_DIR"
exec > >(tee -a "$LOG_FILE") 2>&1

log()  {
    echo "[$(date +%H:%M:%S)] $*";
}
clean_after() {
    # This one is result of the file mount, so removing it
    rm -f "${LOCAL_PROJECT_ROOT}/.env.local.php";
    # This one is also part of the file mount, but created to avoid mounting failure due to "read-only system".
    # The error itself seems a bit inconsistent: sometimes happens without this file, sometimes not.
    rm -f "${LOCAL_PROJECT_ROOT}/.env.local.php"
    # Removing to minimize potential of the file being modified before next run, and it' supposed to be empty
    rm -f "${DEPLOY_ARTIFACTS_DIR}/empty.ini";
}
fail() {
    log "ERROR: $*";
    clean_after;
    exit 1;
}

[ -f "$RSYNC_FILTER_FILE" ]      || fail "Filter file not found: $RSYNC_FILTER_FILE"
[ -f "$SERVICE_PATTERNS_FILE" ]  || fail "Service pattern file not found: $SERVICE_PATTERNS_FILE"
[ -f "$RSYNC_JOBS_FILE" ]        || fail "Rsync jobs file not found: $RSYNC_JOBS_FILE"

load_sync_jobs
[ "${#SYNC_LABELS[@]}" -gt 0 ] || fail "No sync jobs defined in ${RSYNC_JOBS_FILE}."

# ---------------------------------------------------------------------------
# STEP 0 - RESUME CHECK
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
# STEP 1 - LOCAL BUILD: BUN, THEN COMPOSER/SYMFONY
# ---------------------------------------------------------------------------

log "Running Bun build scripts..."
docker compose -f ./compose.yaml -f ./compose.override.yaml run --rm bun bun run deploy || fail "Failed to run Bun."

log "Running Composer/Symfony..."
touch "${DEPLOY_ARTIFACTS_DIR}/.env.local.php"
touch "${LOCAL_PROJECT_ROOT}/.env.local.php"
touch "${DEPLOY_ARTIFACTS_DIR}/empty.ini"
# `update-ca-certificates` is required for importmap to work (otherwise there will be no certificates in the cert store due to tmpfs mount)
docker compose -f ./compose.yaml -f ./compose.predeploy.yaml run --rm --no-deps \
    --entrypoint="" "$SVC_FRANKENPHP" sh -c 'update-ca-certificates && composer install --no-dev --optimize-autoloader && ./bin/console dotenv:dump prod' \
    || fail "PROD vendor/cache/importmap build failed on DEV."
clean_after;

# ---------------------------------------------------------------------------
# STEP 2 - DRY RUN
# ---------------------------------------------------------------------------

declare -A DRY_RUN_OUTPUTS=()
any_changes=false

for label in "${SYNC_LABELS[@]}"; do
    log "Dry run: ${label}..."
    if ! output="$(eval "$(build_rsync_command "$label" "--dry-run")")"; then
        fail "Dry run failed for '${label}' - source: ${LOCAL_PROJECT_ROOT}/${SYNC_SOURCES[$label]}
If this is a build artifact job, check that STEP 1's build actually produced it."
    fi
    DRY_RUN_OUTPUTS["$label"]="$output"
    echo "--- ${label} ---"
    echo "$output"
    [ -n "$output" ] && any_changes=true
done

if ! $any_changes; then
    log "No changes detected across all sync jobs. Nothing to deploy."
    exit 0
fi

CHANGED_PATHS=""
for label in "${SYNC_LABELS[@]}"; do
    CHANGED_PATHS="${CHANGED_PATHS}$(echo "${DRY_RUN_OUTPUTS[$label]}" | sed -E 's/^[^ ]+ //')"$'\n'
done

# ---------------------------------------------------------------------------
# STEP 3 - LOAD SERVICE PATTERNS AND CLASSIFY
# ---------------------------------------------------------------------------

declare -a REBUILD_RULES=()   # "service|pattern"
declare -a RESTART_RULES=()
declare -A ALL_SERVICES_SEEN=()

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

root_compose_changed=false
declare -A need_rebuild=()
declare -A need_restart=()

while IFS= read -r path; do
    [ -z "$path" ] && continue

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
# restart-only entry - it gets recreated either way, see STEP 4.
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
# STEP 4 - BUILD ACTION LIST + SUMMARY + CONFIRMATION
# ---------------------------------------------------------------------------

declare -a rebuild_svc_list=()
for svc in "${!need_rebuild[@]}"; do rebuild_svc_list+=("$svc"); done

declare -a restart_svc_list=()
for svc in "${!need_restart[@]}"; do restart_svc_list+=("$svc"); done

# Rebuild-needing services AND restart-only services are recreated
# together in one combined "up -d" call, so Compose resolves cross-
# service startup order via depends_on for the whole affected set.
declare -a recreate_svc_list=("${rebuild_svc_list[@]}" "${restart_svc_list[@]}")

frankenphp_recreated=false
for svc in "${recreate_svc_list[@]}"; do
    [ "$svc" == "$SVC_FRANKENPHP" ] && frankenphp_recreated=true
done

declare -a ACTIONS=()

# Build first
if [ "${#rebuild_svc_list[@]}" -gt 0 ]; then
    ACTIONS+=("sudo docker compose -f ./compose.yaml build ${rebuild_svc_list[*]}")
fi

# Recreate phase - see the comment on recreate_svc_list above. Restart-
# only services get a full recreate here too, not a lighter "restart":
# safe as long as their real state lives in a volume outside the
# container, true for everything in service-patterns.ini today.
if [ "${#recreate_svc_list[@]}" -gt 0 ]; then
    ACTIONS+=("sudo docker compose -f ./compose.yaml up -d --force-recreate ${recreate_svc_list[*]}")
fi
# Reset unless frankenphp itself was rebuilt/recreated above - a fresh
# process already starts with an empty opcache.
if ! $frankenphp_recreated; then
    ACTIONS+=("sudo docker compose -f ./compose.yaml exec ${SVC_FRANKENPHP} curl -fsS ${OPCACHE_RESET_URL}")
fi
# Remove maintenance flag
ACTIONS+=("rm -f ${MAINTENANCE_FLAG}")

echo ""
echo "===== DEPLOY SUMMARY ====="
echo "- touch ${MAINTENANCE_FLAG}"
for label in "${SYNC_LABELS[@]}"; do
    echo "- $(build_rsync_command "$label" "")"
done
for a in "${ACTIONS[@]}"; do
    echo "- $a"
done
echo "==========================="
echo ""

read -r -p "Proceed with deploy? [y/N]: " CONFIRM
CONFIRM="${CONFIRM:-N}"
if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
    log "Deploy cancelled by user."
    exit 0
fi

# ---------------------------------------------------------------------------
# STEP 5 - MAINTENANCE FLAG ON, THEN REAL RSYNC
# ---------------------------------------------------------------------------

log "Setting maintenance flag on PROD..."
run_remote_action "touch ${MAINTENANCE_FLAG}" \
    || fail "Could not set maintenance flag - aborting before touching PROD further."

for label in "${SYNC_LABELS[@]}"; do
    log "Syncing: ${label}..."
    eval "$(build_rsync_command "$label" "")" \
        || fail "${label} sync failed. Maintenance flag is still set on PROD."
done

# ---------------------------------------------------------------------------
# STEP 6 - WRITE PENDING ACTIONS, THEN RUN ONE BY ONE
# ---------------------------------------------------------------------------

printf '%s\n' "${ACTIONS[@]}" > "$PENDING_FILE"
execute_pending

log "Deploy complete."
