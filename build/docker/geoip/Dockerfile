FROM ghcr.io/maxmind/geoipupdate:latest

ENV GEOIPUPDATE_PRESERVE_FILE_TIMES=1 \
    GEOIPUPDATE_FREQUENCY=24 \
    GEOIPUPDATE_EDITION_IDS="GeoLite2-ASN GeoLite2-City"

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]