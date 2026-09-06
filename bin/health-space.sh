#!/bin/bash

# Check if there is enough space on server

set -euo pipefail

DIR="/var/log"
FLAG_FILE="$DIR/noSpace.flag"
THRESHOLD=5 # percent

mkdir -p "$DIR"

measure() {
    read -r _ total _ free _ < <(df -P -B1 "$DIR" | tail -1)
    percent=$(awk -v f="$free" -v t="$total" 'BEGIN { printf "%.2f", f * 100 / t }')
}

below_threshold() {
    awk -v p="$percent" -v t="$THRESHOLD" 'BEGIN { exit !(p < t) }'
}

human() {
    awk -v b="$1" 'BEGIN {
        n = split("KiB MiB GiB TiB", U, " ")
        s = "B"
        for (i = 1; i <= n && b >= 1024; i++) {
            b /= 1024
            s = U[i]
        }
        printf "%.1f%s", b, s
    }'
}

measure

if below_threshold; then
    if [[ ! -f "$FLAG_FILE" ]]; then
        /etc/supercronic/bin/clean-files.sh
        measure
        if below_threshold; then
            /etc/supercronic/bin/send-mail.sh "[Alert] Low disk space: ${percent}% free" \
                "Free: $(human "$free") / Total: $(human "$total") (${percent}% free)" \
                2
            printf '%s%% of space left' "$percent" > "$FLAG_FILE"
        fi
    fi
elif [[ -f "$FLAG_FILE" ]]; then
    rm -f "$FLAG_FILE"
    /etc/supercronic/bin/send-mail.sh "Disk space restored: ${percent}% free" \
        "Free: $(human "$free") / Total: $(human "$total") (${percent}% free)" \
        1
fi
