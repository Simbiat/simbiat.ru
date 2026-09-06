#!/bin/bash

# Check if there is enough space on server

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

# --- Environment config ---
prod=false
if [[ "$APP_ENV" == 'prod' ]]; then
    prod=true
elif [[ -z "$APP_ENV" ]]; then
    echo "Warning: APP_ENV not found, assuming non-prod" >&2
fi

# --- Priority headers ---
if ! $prod; then
    smtp_priority='Non-Urgent'
    importance='Low'
    subject="[Test] $subject"
elif (( priority > 1 )); then
    smtp_priority='Urgent'
    importance='High'
elif (( priority < 1 )); then
    smtp_priority='Non-Urgent'
    importance='Low'
else
    smtp_priority='Normal'
    importance='Normal'
fi

curl -s --url "$MAILER_DSN" \
    --mail-from "$PROTON_USER" \
    --mail-rcpt "$ADMIN_EMAIL" \
    -T <(printf 'From: %s\r\nTo: %s\r\nSubject: %s\r\nPriority: %s\r\nImportance: %s\r\n\r\n%s\r\n' \
          "$PROTON_USER" "$ADMIN_EMAIL" "$subject" "$smtp_priority" "$importance" "$body")
