#!/bin/bash
set -e

LOGFILE="/usr/local/logs/mariadb.log"
exec > >(tee -a "$LOGFILE") 2>&1

#Required to pass environment variables to crontab
printenv | grep -v "no_proxy" > /run/cron-env &&
#We need to ensure that buffer pool file exists
touch /var/lib/mysql/ib_buffer_pool &&
chown mysql:mysql /var/lib/mysql/ib_buffer_pool &&
#We need to start cron service before the endpoint
cron &&
#If maintenance flag is found, it implies crash during maintenance: rename it to indicate this
if [ -f /usr/local/logs/maintenance.flag ]; then
  mv /usr/local/logs/maintenance.flag /usr/local/logs/backup_crash.flag
fi

exec docker-entrypoint.sh mariadbd