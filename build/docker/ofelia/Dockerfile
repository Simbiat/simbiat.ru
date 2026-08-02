FROM ghcr.io/netresearch/ofelia:latest

# Default values
ARG PUID=1000
ARG PGID=1000

# Create custom user with UID and GID matching host (note, that this uses BusyBox commands, unlike other containers)
RUN addgroup -g "${PGID}" ofelia && adduser -u "${PUID}" -G ofelia -h /home/ofelia -s /bin/bash -D ofelia

USER ofelia

ENTRYPOINT exec ofelia daemon --config=/etc/ofelia/config.ini 2>&1 | tee -a /var/log/ofelia.log