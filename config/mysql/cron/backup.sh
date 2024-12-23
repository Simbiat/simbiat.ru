#!/bin/sh

if [ "$WEB_SERVER_TEST" != "true" ]; then
  currentDate=$(date +%Y.%m.%d)
  logFile=/usr/local/logs/backup-$currentDate.log
  physBackup=/usr/local/backups/physical
  logicBackup=/usr/local/backups
  tablesOrder=$(cat /usr/local/DDL/00-recommended_table_order.txt)
  logicalName="daily"
  if [ "$(date +%u)" -eq 1 ]; then
      logicalName="weekly"
  fi
  if [ "$(date +%d)" -eq 01 ]; then
        logicalName="monthly"
    fi

  {
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Cleaning files...";
    rm -rf $logicBackup/*.sql;
    rm -rf $logicBackup/*.7z;
    rm -rf $physBackup;
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up users for $currentDate...";
    mariadb-dump --all-tablespaces --opt --add-drop-database --default-character-set=utf8mb4 --flush-privileges --flush-logs --tz-utc --quote-names --single-transaction --insert-ignore --system=users 2>> "$logFile" 1>> "$logicBackup/$currentDate-users.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping users for $currentDate...";
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -p"${MARIADB_BACKUP_PASSWORD}" "$logicBackup/$currentDate-users.7z" "$logicBackup/$currentDate-users.sql";
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up data for $currentDate (logical)...";
    mariadb-dump --all-tablespaces --opt --add-drop-database --default-character-set=utf8mb4 --flush-privileges --flush-logs --tz-utc --quote-names --single-transaction --insert-ignore --routines --triggers --databases simbiatr_simbiat --tables $tablesOrder 2>> "$logFile" 1>> "$logicBackup/$currentDate-${logicalName}.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping data for $currentDate (logical)..."
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -p"${MARIADB_BACKUP_PASSWORD}" "$logicBackup/$currentDate-${logicalName}.7z" "$logicBackup/$currentDate-${logicalName}.sql"
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backing up full database for $currentDate (physical)..."
    mariadb-backup --backup --target-dir=$physBackup --kill-long-queries-timeout=300 --extended-validation --lock-ddl-per-table
    echo "Owning for $currentDate..."
    chown mysql:mysql $physBackup -R
    echo "Preparing $currentDate..."
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Preparing backup for $currentDate (physical)..."
    mariadb-backup --prepare --target-dir=$physBackup
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Zipping backup for $currentDate (physical)..."
    7z a -aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -p"${MARIADB_BACKUP_PASSWORD}" "$logicBackup/$currentDate-physical.7z" $physBackup
    echo "[$(date +%Y-%m-%dT%H:%M:%S%z)] Backup completed"
  } >> "$logFile" 2>&1
fi
exit