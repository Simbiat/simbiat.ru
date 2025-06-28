#!/bin/sh

#We do not want to run this regularly on test environment
if [ "$WEB_SERVER_TEST" != "true" ]; then
  #Ensure working directory is changed
  cd /app || exit
  #Run the script itself
  /usr/local/bin/php -f /app/bin/Cron.php
fi
exit
