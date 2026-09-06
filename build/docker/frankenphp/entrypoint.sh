#!/bin/bash
set -e

if [ ! -s /etc/ssl/certs/ca-certificates.crt ]; then
    cp -a /usr/local/share/ca-bundle-backup/. /etc/ssl/certs/
fi

mkdir -p /tmp/opcache;
mkdir -p /tmp/upload;
mkdir -p /tmp/sessions;

exec docker-php-entrypoint --config /app/config/caddy.json
