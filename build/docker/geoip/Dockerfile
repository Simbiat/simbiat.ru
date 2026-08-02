FROM ghcr.io/maxmind/geoipupdate:latest

ENV GEOIPUPDATE_PRESERVE_FILE_TIMES=1 \
    GEOIPUPDATE_FREQUENCY=24 \
    GEOIPUPDATE_EDITION_IDS="GeoLite2-ASN GeoLite2-City"

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

HEALTHCHECK --start-period=60s --interval=1h --timeout=5s --retries=3 \
            CMD stat -c%s /usr/share/GeoIP/GeoLite2-City.mmdb | awk '{exit !($1 > 10000)}' && stat -c%s /usr/share/GeoIP/GeoLite2-ASN.mmdb | awk '{exit !($1 > 10000)}'

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]