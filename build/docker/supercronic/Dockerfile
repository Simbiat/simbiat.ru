FROM alpine:latest

# Default values
ARG PUID=1000
ARG PGID=1000

RUN apk add --no-cache docker-cli curl bash
ADD https://github.com/aptible/supercronic/releases/latest/download/supercronic-linux-amd64 /usr/local/bin/supercronic
RUN chmod +x /usr/local/bin/supercronic

# Create custom user with UID and GID matching host (note, that this uses BusyBox commands, unlike other containers)
RUN addgroup -g "${PGID}" supercronic && adduser -u "${PUID}" -G supercronic -h /home/supercronic -s /bin/bash -D supercronic

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chown supercronic:supercronic -R /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD find /tmp/heartbeat -mmin -2 -type f | grep -q . || exit 1

USER supercronic

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]