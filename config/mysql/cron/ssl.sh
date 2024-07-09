#!/bin/sh
#These are meant to be used only for internal communication and by admins with access, so self-signed seem valid

#Get current date
currentDate=$(date +%Y.%m.%d)
logFile=/usr/local/logs/ssl-$currentDate.log
keysDir=/usr/local/keys
keyPrefix=
if [ "$WEB_SERVER_TEST" != "false" ]; then
  keyPrefix=test_
fi

#Generate CA
echo Generating CA for $currentDate... >> $logFile 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/${keyPrefix}ca.key -subj /CN=${MARIADB_TLS_SETTING_CA_NAME} -out $keysDir/${keyPrefix}ca.req >> $logFile 2>&1
openssl rsa -in $keysDir/${keyPrefix}ca.key -out $keysDir/${keyPrefix}ca.key >> $logFile 2>&1
openssl x509 -sha256 -days 90 -set_serial 1 -req -in $keysDir/${keyPrefix}ca.req -signkey $keysDir/${keyPrefix}ca.key -out $keysDir/${keyPrefix}ca.crt >> $logFile 2>&1

#Generate server certificate
echo Generating Server for $currentDate... >> $logFile 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/${keyPrefix}server.key -subj /CN=${MARIADB_TLS_SETTING_SERVER_NAME} -addext "subjectAltName=${MARIADB_TLS_SETTING_SERVER_ALIAS}" -out $keysDir/${keyPrefix}server.req >> $logFile 2>&1
openssl rsa -in $keysDir/${keyPrefix}server.key -out $keysDir/${keyPrefix}server.key >> $logFile 2>&1
openssl x509 -sha256 -days 90 -set_serial 2 -req -in $keysDir/${keyPrefix}server.req -CA $keysDir/${keyPrefix}ca.crt -CAkey $keysDir/${keyPrefix}ca.key -out $keysDir/${keyPrefix}server.crt >> $logFile 2>&1

#Generate client certificate
echo Generating Client for $currentDate... >> $logFile 2>&1
openssl req -newkey rsa:2048 -nodes -keyout $keysDir/${keyPrefix}client.key -subj /CN=${MARIADB_TLS_SETTING_CLIENT_NAME} -out $keysDir/${keyPrefix}client.req >> $logFile 2>&1
openssl rsa -in $keysDir/${keyPrefix}client.key -out $keysDir/${keyPrefix}client.key >> $logFile 2>&1
openssl x509 -sha256 -days 90 -set_serial 3 -req -in $keysDir/${keyPrefix}client.req -CA $keysDir/${keyPrefix}ca.crt -CAkey $keysDir/${keyPrefix}ca.key -out $keysDir/${keyPrefix}client.crt >> $logFile 2>&1

#Own the files (otherwise MariaDB can fail to read private keys)
echo Owning for $currentDate... >> $logFile 2>&1
chown mysql:mysql $keysDir/ -R >> $logFile 2>&1

if [ ! -z "$1" ]; then
  #Flush SSL
  echo Flushing for $currentDate... >> $logFile 2>&1
  mariadb-admin flush-ssl >> $logFile 2>&1
fi
exit
