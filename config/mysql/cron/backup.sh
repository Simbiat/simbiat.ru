#!/bin/sh

if [ "$WEB_SERVER_TEST" != "true" ]; then
  currentDate=$(date +%Y.%m.%d)
  logFile=/usr/local/logs/backup-$currentDate.log
  physBackup=/usr/local/backups/physical
  logicBackup=/usr/local/backups
  tablesOrder=$(cat /usr/local/DDL/000-recommended_table_order.txt)
  optimizationQueries=/usr/local/backups/optimization_commands.sql
  logicalName="daily"
  if [ "$(date +%u)" -eq 1 ]; then
    logicalName="weekly"
  fi
  if [ "$(date +%d)" -eq 01 ]; then
    logicalName="monthly"
  fi

  set -e
  trap 'catch $? $LINENO' EXIT
  catch() {
    # shellcheck disable=SC2317
    if [ "$1" != "0" ]; then
      echo "Error $1 occurred on $2">> "$logFile" 2>&1
      mv /usr/local/backups/maintenance.flag /usr/local/backups/crash.flag
    fi
  }

  touch /usr/local/backups/maintenance.flag
  {
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Cleaning files...";
    rm -rf $logicBackup/*.sql;
    rm -rf $logicBackup/*.7z;
    rm -rf $physBackup;
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Enabling maintenance mode...";
    mariadb --execute "UPDATE \`${DATABASE_NAME}\`.\`sys__settings\` SET \`value\` = 1 WHERE \`setting\` = 'maintenance';"
    if [ -f ${optimizationQueries} ]; then
      echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Running optimization script...";
      mariadb < ${optimizationQueries}
      rm -f ${optimizationQueries}
    fi
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up users for $currentDate...";
    mariadb-dump --all-tablespaces --opt --add-drop-database --default-character-set=utf8mb4 --flush-privileges --flush-logs --tz-utc --quote-names --single-transaction --insert-ignore --system=users 2>> "$logFile" 1>> "$logicBackup/$currentDate-users.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up data for $currentDate (logical)...";
    # shellcheck disable=SC2086
    # quoting tablesOrder will break things, so suppressing inspection for this.
    mariadb-dump --all-tablespaces --opt --add-drop-database --default-character-set=utf8mb4 --flush-privileges --flush-logs --tz-utc --quote-names --single-transaction --insert-ignore --routines --triggers --databases "${DATABASE_NAME}" --tables $tablesOrder 2>> "$logFile" 1>> "$logicBackup/$currentDate-${logicalName}.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up full database for $currentDate (physical)..."
    mariadb-backup --backup --target-dir="$physBackup" --kill-long-queries-timeout=300 --extended-validation --lock-ddl-per-table --parallel=4
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Owning for $currentDate..."
    chown mysql:mysql $physBackup -R
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Preparing backup for $currentDate (physical)..."
    mariadb-backup --prepare --target-dir=$physBackup
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Disabling maintenance mode...";
    mariadb --execute "UPDATE \`${DATABASE_NAME}\`.\`sys__settings\` SET \`value\` = 0 WHERE \`setting\` = 'maintenance';"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping users for $currentDate...";
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -p"${MARIADB_BACKUP_PASSWORD}" "$logicBackup/$currentDate-users.7z" "$logicBackup/$currentDate-users.sql";
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping data for $currentDate (logical)..."
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -p"${MARIADB_BACKUP_PASSWORD}" "$logicBackup/$currentDate-${logicalName}.7z" "$logicBackup/$currentDate-${logicalName}.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping backup for $currentDate (physical)..."
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -p"${MARIADB_BACKUP_PASSWORD}" "$logicBackup/$currentDate-physical.7z" $physBackup
    #For some reason physical backup is no longer being deleted after compression, even though it should be, so using a separate command to do that, and also to remove any loose SQL files (should not happen normally)
    rm -rf $logicBackup/*.sql;
    rm -rf $physBackup;
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backup completed"
  } >> "$logFile" 2>&1
  rm -f /usr/local/backups/maintenance.flag
fi
exit