#!/bin/sh

#Source environments (required for cron)
. /etc/mysql/conf.d/mariadb.env

#These are meant to be used only for internal communication and by admins with access, so self-signed seem valid

#Get current date
currentDate=$(date +%Y%m%d)
logsDir=/usr/local/logs
keysDir=$MARIADB_KEYS_DIR

#Generate CA
echo Generating CA for $currentDate... >> $logsDir/ssl.log 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/ca.key -subj /CN=${MARIADB_TLS_SETTING_CA_NAME} -out $keysDir/ca.req >> $logsDir/ssl.log 2>&1
openssl rsa -in $keysDir/ca.key -out $keysDir/ca.key >> $logsDir/ssl.log 2>&1
openssl x509 -sha256 -days 90 -set_serial 1 -req -in $keysDir/ca.req -signkey $keysDir/ca.key -out $keysDir/ca.crt >> $logsDir/ssl.log 2>&1

#Generate server certificate
echo Generating Server for $currentDate... >> $logsDir/ssl.log 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/server.key -subj /CN=${MARIADB_TLS_SETTING_SERVER_NAME} -addext "subjectAltName=${MARIADB_TLS_SETTING_SERVER_ALIAS}" -out $keysDir/server.req >> $logsDir/ssl.log 2>&1
openssl rsa -in $keysDir/server.key -out $keysDir/server.key >> $logsDir/ssl.log 2>&1
openssl x509 -sha256 -days 90 -set_serial 2 -req -in $keysDir/server.req -CA $keysDir/ca.crt -CAkey $keysDir/ca.key -out $keysDir/server.crt >> $logsDir/ssl.log 2>&1

#Generate client certificate
echo Generating Client for $currentDate... >> $logsDir/ssl.log 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/client.key -subj /CN=${MARIADB_TLS_SETTING_CLIENT_NAME} -out $keysDir/client.req >> $logsDir/ssl.log 2>&1
openssl rsa -in $keysDir/client.key -out $keysDir/client.key >> $logsDir/ssl.log 2>&1
openssl x509 -sha256 -days 90 -set_serial 3 -req -in $keysDir/client.req -CA $keysDir/ca.crt -CAkey $keysDir/ca.key -out $keysDir/client.crt >> $logsDir/ssl.log 2>&1

#Own the files (otherwise MariaDB can fail to read private keys)
echo Owning for $currentDate... >> $logsDir/ssl.log 2>&1
chown mysql:mysql $keysDir/ -R >> $logsDir/ssl.log 2>&1

if [ ! -z "$1" ]; then
  #Flush SSL
  echo Flushing for $currentDate... >> $logsDir/ssl.log 2>&1
  mariadb-admin flush-ssl >> $logsDir/ssl.log 2>&1
fi
exit
