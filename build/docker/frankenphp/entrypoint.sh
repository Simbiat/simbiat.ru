#!/bin/bash
set -e

# Create some temp directories. Had cases, when they were missing despite being in the `Dockerfile`
# It also is required now, since `/tmp` is a volume
mkdir -p /tmp/upload/;
mkdir -p /tmp/sessions/;
mkdir -p /tmp/soap/;
mkdir -p /tmp/opcache/;

exec docker-php-entrypoint --config /usr/local/etc/php/caddy.json