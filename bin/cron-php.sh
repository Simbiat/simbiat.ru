#!/bin/sh

#Do not attempt anything if maintenance flag is present, commands are likely to fail
if [ -f "/var/log/db_maintenance.flag" ]; then
    exit 0
fi
exec /etc/supercronic/bin/cron-wrapper.sh frankenphp /app/bin/console "$@"
