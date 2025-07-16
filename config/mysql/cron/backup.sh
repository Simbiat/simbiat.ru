#!/bin/sh

if [ "$WEB_SERVER_TEST" != "true" ]; then
  current_date=$(date +%Y.%m.%d)
  log_file=/usr/local/logs/backup-$current_date.log
  physical_backup=/usr/local/backups/physical
  logical_backup=/usr/local/backups
  tables_order=$(cat /usr/local/DDL/000-recommended_table_order.txt)
  optimization_queries=/usr/local/backups/optimization_commands.sql
  logical_name="daily"
  if [ "$(date +%u)" -eq 1 ]; then
    logical_name="weekly"
  fi
  if [ "$(date +%d)" -eq 01 ]; then
    logical_name="monthly"
  fi

  set -e
  trap 'catch $? $LINENO' EXIT
  catch() {
    # shellcheck disable=SC2317
    if [ "$1" != "0" ]; then
      echo "Error $1 occurred on $2">> "$log_file" 2>&1
      echo "Error $1 occurred on $2">> /usr/local/backups/maintenance.flag 2>&1
      mv /usr/local/backups/maintenance.flag /usr/local/backups/crash.flag
    fi
  }

  touch /usr/local/backups/maintenance.flag
  {
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Cleaning files...";
    rm -rf $logical_backup/*.sql;
    rm -rf $logical_backup/*.7z;
    rm -rf $physical_backup;
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Enabling maintenance mode...";
    mariadb --execute "UPDATE \`${DATABASE_NAME}\`.\`sys__settings\` SET \`value\` = 1 WHERE \`setting\` = 'maintenance';"
    if [ -f ${optimization_queries} ]; then
      echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Running optimization script...";
      mariadb < ${optimization_queries}
      rm -f ${optimization_queries}
    fi
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up users for $current_date...";
    mariadb-dump --all-tablespaces --opt --add-drop-database --default-character-set=utf8mb4 --flush-privileges --flush-logs --tz-utc --quote-names --single-transaction --insert-ignore --system=users 2>> "$log_file" 1>> "$logical_backup/$current_date-users.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up data for $current_date (logical)...";
    # shellcheck disable=SC2086
    # quoting tables_order will break things, so suppressing inspection for this.
    mariadb-dump --all-tablespaces --opt --add-drop-database --default-character-set=utf8mb4 --flush-privileges --flush-logs --tz-utc --quote-names --single-transaction --insert-ignore --routines --triggers --databases "${DATABASE_NAME}" --tables $tables_order 2>> "$log_file" 1>> "$logical_backup/$current_date-${logical_name}.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up full database for $current_date (physical)..."
    mariadb-backup --backup --target-dir="$physical_backup" --kill-long-queries-timeout=300 --extended-validation --lock-ddl-per-table --parallel=4
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Owning for $current_date..."
    chown mysql:mysql $physical_backup -R
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Preparing backup for $current_date (physical)..."
    mariadb-backup --prepare --target-dir=$physical_backup
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Disabling maintenance mode...";
    mariadb --execute "UPDATE \`${DATABASE_NAME}\`.\`sys__settings\` SET \`value\` = 0 WHERE \`setting\` = 'maintenance';"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping users for $current_date...";
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -mmemuse=128m -mhe -mtc -mta -mtm -mmt=on -p"${MARIADB_BACKUP_PASSWORD}" "$logical_backup/$current_date-users.7z" "$logical_backup/$current_date-users.sql";
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping data for $current_date (logical)..."
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -mmemuse=128m -mhe -mtc -mta -mtm -mmt=on -p"${MARIADB_BACKUP_PASSWORD}" "$logical_backup/$current_date-${logical_name}.7z" "$logical_backup/$current_date-${logical_name}.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping backup for $current_date (physical)..."
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -mmemuse=128m -mhe -mtc -mta -mtm -mmt=on -p"${MARIADB_BACKUP_PASSWORD}" "$logical_backup/$current_date-physical.7z" $physical_backup
    #For some reason physical backup is no longer being deleted after compression, even though it should be, so using a separate command to do that, and also to remove any loose SQL files (should not happen normally)
    rm -rf $logical_backup/*.sql;
    rm -rf $physical_backup;
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backup completed"
  } >> "$log_file" 2>&1
  rm -f /usr/local/backups/maintenance.flag
fi
exit