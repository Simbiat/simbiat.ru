#!/bin/sh

#Based on official script, but without automatic shutdown (can otherwise exceed daily limit),
#and touch of the database files (required for CrowdSec, since otherwise it will map the files as directories)

pid=0
database_dir=/usr/share/GeoIP
log_dir="/tmp/geoipupdate"
log_file="$log_dir/.healthcheck"
flags="--output"
frequency=$((GEOIPUPDATE_FREQUENCY * 60 * 60))
export GEOIPUPDATE_CONF_FILE=""

if [ -z "$GEOIPUPDATE_DB_DIR" ]; then
  export GEOIPUPDATE_DB_DIR="$database_dir"
fi

if [ -z "$GEOIPUPDATE_ACCOUNT_ID" ] && [ -z  "$GEOIPUPDATE_ACCOUNT_ID_FILE" ]; then
    echo "ERROR: You must set the environment variable GEOIPUPDATE_ACCOUNT_ID or GEOIPUPDATE_ACCOUNT_ID_FILE!"
    exit 1
fi

if [ -z "$GEOIPUPDATE_LICENSE_KEY" ] && [ -z  "$GEOIPUPDATE_LICENSE_KEY_FILE" ]; then
    echo "ERROR: You must set the environment variable GEOIPUPDATE_LICENSE_KEY or GEOIPUPDATE_LICENSE_KEY_FILE!"
    exit 1
fi

if [ -z "$GEOIPUPDATE_EDITION_IDS" ]; then
    echo "ERROR: You must set the environment variable GEOIPUPDATE_EDITION_IDS!"
    exit 1
fi

mkdir -p $log_dir

touch /usr/share/GeoIP/GeoLite2-City.mmdb
touch /usr/share/GeoIP/GeoLite2-ASN.mmdb

while true; do
    echo "# STATE: Running geoipupdate"
    /usr/bin/geoipupdate $flags 1>$log_file
    if [ "$frequency" -eq 0 ]; then
        break
    fi

    echo "# STATE: Sleeping for $GEOIPUPDATE_FREQUENCY hours"
    sleep "$frequency" &
    pid=$!
    wait $!
done