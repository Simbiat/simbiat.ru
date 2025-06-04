#!/bin/sh

#We do not want to run this regularly on test environment
if [ "$WEB_SERVER_TEST" != "true" ]; then
  #Ensure working directory is changed
  cd /app
  #Run the script itself
  /usr/local/bin/php -f /app/bin/Minute.php
fi
exit
