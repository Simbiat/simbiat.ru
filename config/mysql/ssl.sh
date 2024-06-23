#!/bin/sh

#Source environments (required for cron)
. /etc/mysql/conf.d/mariadb.env

#These are meant to be used only for internal communication and by admins with access, so self-signed seem valid

#Get current date
currentDate=$(date +%Y%m%d)
logsDir=/usr/local/logs
keysDir=/usr/local/keys
keyPrefix=
if [ "$WEB_SERVER_TEST" != "false" ]; then
  keyPrefix=test_
fi

#Generate CA
echo Generating CA for $currentDate... >> $logsDir/ssl.log 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/${keyPrefix}ca.key -subj /CN=${MARIADB_TLS_SETTING_CA_NAME} -out $keysDir/${keyPrefix}ca.req >> $logsDir/ssl.log 2>&1
openssl rsa -in $keysDir/${keyPrefix}ca.key -out $keysDir/${keyPrefix}ca.key >> $logsDir/ssl.log 2>&1
openssl x509 -sha256 -days 90 -set_serial 1 -req -in $keysDir/${keyPrefix}ca.req -signkey $keysDir/${keyPrefix}ca.key -out $keysDir/${keyPrefix}ca.crt >> $logsDir/ssl.log 2>&1

#Generate server certificate
echo Generating Server for $currentDate... >> $logsDir/ssl.log 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/${keyPrefix}server.key -subj /CN=${MARIADB_TLS_SETTING_SERVER_NAME} -addext "subjectAltName=${MARIADB_TLS_SETTING_SERVER_ALIAS}" -out $keysDir/${keyPrefix}server.req >> $logsDir/ssl.log 2>&1
openssl rsa -in $keysDir/${keyPrefix}server.key -out $keysDir/${keyPrefix}server.key >> $logsDir/ssl.log 2>&1
openssl x509 -sha256 -days 90 -set_serial 2 -req -in $keysDir/${keyPrefix}server.req -CA $keysDir/${keyPrefix}ca.crt -CAkey $keysDir/${keyPrefix}ca.key -out $keysDir/${keyPrefix}server.crt >> $logsDir/ssl.log 2>&1

#Generate client certificate
echo Generating Client for $currentDate... >> $logsDir/ssl.log 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/${keyPrefix}client.key -subj /CN=${MARIADB_TLS_SETTING_CLIENT_NAME} -out $keysDir/${keyPrefix}client.req >> $logsDir/ssl.log 2>&1
openssl rsa -in $keysDir/${keyPrefix}client.key -out $keysDir/${keyPrefix}client.key >> $logsDir/ssl.log 2>&1
openssl x509 -sha256 -days 90 -set_serial 3 -req -in $keysDir/${keyPrefix}client.req -CA $keysDir/${keyPrefix}ca.crt -CAkey $keysDir/${keyPrefix}ca.key -out $keysDir/${keyPrefix}client.crt >> $logsDir/ssl.log 2>&1

#Own the files (otherwise MariaDB can fail to read private keys)
echo Owning for $currentDate... >> $logsDir/ssl.log 2>&1
chown mysql:mysql $keysDir/ -R >> $logsDir/ssl.log 2>&1

if [ ! -z "$1" ]; then
  #Flush SSL
  echo Flushing for $currentDate... >> $logsDir/ssl.log 2>&1
  mariadb-admin flush-ssl >> $logsDir/ssl.log 2>&1
fi
exit
