#!/bin/sh
set -e

#Before starting the container, it's better to clear the session files. It looks like otherwise the service can pick up older settings (unless manually clearing session data).
#This is also important in case something happens to the phpMyAdmin database/tables, and they got removed, because otherwise it will keep showing error without allowing to fix it directly through the UI.
rm -f -r /sessions/* ;
exec /docker-entrypoint.sh apache2-foreground