#!/bin/bash
set -e

LOGFILE="/var/log/cron.log"
touch "$LOGFILE"
chown supercronic:supercronic "$LOGFILE"
exec > >(tee -a "$LOGFILE") 2>&1

exec supercronic -inotify -overlapping -passthrough-logs /etc/supercronic/config/supercronic.cron