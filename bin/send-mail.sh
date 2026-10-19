#!/bin/bash

set -a; source "$(dirname "${BASH_SOURCE[0]}")/../.env"; set +a
if [[ -f "$(dirname "${BASH_SOURCE[0]}")/../.env.dev" ]]; then
    set -a; source "$(dirname "${BASH_SOURCE[0]}")/../.env.dev"; set +a
fi
set -euo pipefail

if [[ $# -ne 3 ]]; then
    echo "Usage: ${0##*/} <subject> <body> <priority>" >&2
    exit 1
fi

subject="$1"
body="$2"
priority="$3"

# Check that postfix container is up
CHECK_CONTAINER="/etc/supercronic/bin/check-container.sh"
RC=0
"$CHECK_CONTAINER" "postfix" || RC=$?
if [ "$RC" -ne 0 ]; then
    exit "$RC"
fi

# --- Environment config ---
prod=false
if [[ "${APP_ENV:-}" == 'prod' ]]; then
    prod=true
elif [[ -z "${APP_ENV:-}" ]]; then
    echo 'Warning: APP_ENV not found, assuming non-prod' >&2
fi

# --- Priority headers ---
if (( priority > 1 )); then
    smtp_priority='Urgent'
    importance='High'
elif (( priority < 1 )); then
    smtp_priority='Non-Urgent'
    importance='Low'
else
    smtp_priority='Normal'
    importance='Normal'
fi
# --- Non-prod mails ---
if ! $prod; then
    smtp_priority='Non-Urgent'
    importance='Low'
    subject="[Test] $subject"
    body="************* This email is from test environment *************

$body"
fi

# Sanitize new lines, since some servers expect `\r\n`. Unlikely to be relevant for these alerts, but better be on safer side.
body=${body//$'\r'/}
body=${body//$'\n'/$'\r\n'}

curl -sS --url "$MAILER_DSN" \
    --mail-from "$PROTON_USER" \
    --mail-rcpt "$ADMIN_EMAIL" \
    --connect-timeout 30 \
    --max-time 120 \
    -T <(printf 'From: %s\r\nTo: %s\r\nSubject: %s\r\nPriority: %s\r\nImportance: %s\r\n\r\n%s\r\n' \
          "$PROTON_USER" "$ADMIN_EMAIL" "$subject" "$smtp_priority" "$importance" "$body")
