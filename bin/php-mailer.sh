#!/bin/sh

#We do not want to run this regularly on test environment
if [ "$WEB_SERVER_TEST" != "true" ]; then
  #Run the script itself and ensure that xDebug is off (needed on DEV only, does not hurt PROD)
  /usr/local/bin/php -d xdebug.mode=off -f /app/src/Command/Mailer.php
fi
exit
