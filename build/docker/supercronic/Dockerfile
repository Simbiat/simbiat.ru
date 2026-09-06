FROM alpine:3.23.5

# Default values
ARG PUID=1000
ARG PGID=1000

RUN apk add --no-cache docker-cli curl bash
#TODO: Update version when Supercronic has a new release
ADD https://github.com/aptible/supercronic/releases/download/v0.2.49/supercronic-linux-amd64 /usr/local/bin/supercronic
RUN chmod +x /usr/local/bin/supercronic

# Create custom user with UID and GID matching host (note, that this uses BusyBox commands, unlike other containers)
RUN addgroup -g "${PGID}" supercronic && adduser -u "${PUID}" -G supercronic -h /home/supercronic -s /bin/bash -D supercronic

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chown supercronic:supercronic -R /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD find /tmp/heartbeat -mmin -2 -type f | grep -q . || exit 1

# Address of docker socket proxy
ENV DOCKER_HOST="tcp://socket-proxy:2375"

USER supercronic

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
