FROM ghcr.io/maxmind/geoipupdate:latest

# Default values
ARG PUID=1000
ARG PGID=1000

ENV GEOIPUPDATE_PRESERVE_FILE_TIMES=1 \
    GEOIPUPDATE_FREQUENCY=24 \
    GEOIPUPDATE_EDITION_IDS="GeoLite2-ASN GeoLite2-City"

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 755 /usr/local/bin/entrypoint.sh

# Create custom user with UID and GID matching host (note, that this uses BusyBox commands, unlike other containers)
RUN addgroup -g "${PGID}" geoip && adduser -u "${PUID}" -G geoip -h /home/geoip -s /bin/bash -D geoip

HEALTHCHECK --start-period=60s --interval=1h --timeout=5s --retries=3 \
            CMD stat -c%s /usr/share/GeoIP/GeoLite2-City.mmdb | awk '{exit !($1 > 10000)}' && stat -c%s /usr/share/GeoIP/GeoLite2-ASN.mmdb | awk '{exit !($1 > 10000)}'

USER geoip

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
