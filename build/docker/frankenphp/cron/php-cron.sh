#!/bin/sh
# Import environment variables
set -a; . /run/cron-env; set +a

#We do not want to run this regularly on test environment
if [ "$WEB_SERVER_TEST" != "true" ]; then
  #Ensure working directory is changed
  cd /app || exit
  #Run the script itself
  /usr/local/bin/php -f /app/src/Command/Cron.php
fi
exit
