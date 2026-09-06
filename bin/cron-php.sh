#!/bin/sh

exec /etc/supercronic/bin/cron-wrapper.sh frankenphp /app/bin/console "$@"