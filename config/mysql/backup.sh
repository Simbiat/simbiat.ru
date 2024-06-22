#!/bin/sh

#Source environments (required for cron)
. /etc/mysql/conf.d/mariadb.env
if [ "$WEB_SERVER_TEST" != "true" ]
  currentDate=$(date +%Y.%m.%d)
  logFile=/usr/local/logs/backup-$currentDate.log
  physBackup=/usr/local/backups/physical
  logicBackup=/usr/local/backups
  tablesOrder=$(cat /usr/local/DDL/00-recommended_table_order.txt)
  dumpSetting="--all-tablespaces --opt --add-drop-database --default-character-set=utf8mb4 --flush-privileges --flush-logs --tz-utc --quote-names --single-transaction --insert-ignore"
  zipSettings="-aoa -y -r -stl -sdel -sse -ssp -ssw -ssc -bt -m0=LZMA2 -p${BACKUP_PASSWORD}"

  echo Cleaning files... >> $logFile 2>&1
  rm -rf $logicBackup/$currentDate-users.sql.gz >> $logFile 2>&1
  rm -rf $logicBackup/$currentDate-data.sql.gz >> $logFile 2>&1
  rm -rf $physBackup >> $logFile 2>&1
  echo "Backing up users for $currentDate (logical)..." >> $logFile 2>&1
  mariadb-dump $dumpSetting --system=users 2>> $logFile | 7z a ${zipSettings} -si$currentDate-users.sql $logicBackup/$currentDate-users.7z >> $logFile 2>&1
  echo "Backing up data for $currentDate (logical)..." >> $logFile 2>&1
  mariadb-dump $dumpSetting --routines --triggers --databases simbiatr_simbiat --tables $tablesOrder 2>> $logFile | 7z a ${zipSettings} -si$currentDate-data.sql $logicBackup/$currentDate-data.7z >> $logFile 2>&1
  echo "Backing up full database for $currentDate (physical)..." >> $logFile 2>&1
  mariadb-backup --backup --target-dir=$physBackup --kill-long-queries-timeout=300 --extended-validation --lock-ddl-per-table  >> $logFile 2>&1
  echo Owning for $currentDate... >> $logFile 2>&1
  chown mysql:mysql $physBackup -R >> $logFile 2>&1
  echo Preparing $currentDate... >> $logFile 2>&1
  echo "Preparing backup for $currentDate (physical)..." >> $logFile 2>&1
  mariadb-backup --prepare --target-dir=$physBackup >> $logFile 2>&1
  echo "Zipping backup for $currentDate (physical)..." >> $logFile 2>&1
  7z a ${zipSettings} $physBackup.7z $physBackup >> $logFile 2>&1
fi
exit
