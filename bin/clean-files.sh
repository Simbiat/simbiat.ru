#!/bin/bash

# Clean old files from various folders

clean_dir() {
    local dir="$1"
    local age_min="${2:-1440}"
    shift 2 2>/dev/null || shift $# 2>/dev/null

    if [[ -z "$dir" || ! -d "$dir" ]]; then
        echo "Usage: clean_dir <directory> [age_minutes] [excluded_subdir ...]" >&2
        return 1
    fi

    dir="${dir%/}"

    local exclusions=("$@")
    local prune=() d i=0
    for d in "${exclusions[@]}"; do
        (( i++ > 0 )) && prune+=(-o)
        prune+=(-path "$dir/$d")
    done

    if (( ${#prune[@]} > 0 )); then
        find "$dir" -mindepth 1 \( "${prune[@]}" \) -prune \
            -o -type f -mmin +"$age_min" -delete
        find "$dir" -mindepth 1 \( "${prune[@]}" \) -prune \
            -o -type d -empty -delete
    else
        find "$dir" -mindepth 1 -type f -mmin +"$age_min" -delete
        find "$dir" -mindepth 1 -type d -empty -delete
    fi
}

clean_dir /to_clean/temp 1440 opcache sessions upload
clean_dir /to_clean/temp/sessions 1440
clean_dir /to_clean/temp/upload 1440
clean_dir /to_clean/temp/opcache 144000
clean_dir /to_clean/crests 14400
