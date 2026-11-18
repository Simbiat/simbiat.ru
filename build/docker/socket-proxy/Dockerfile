FROM tecnativa/docker-socket-proxy:34

HEALTHCHECK --interval=5m --timeout=5s --start-period=15s --retries=3 \
        CMD test -S /var/run/docker.sock && pgrep haproxy >/dev/null || exit 1

# Everything defaults to 0/disabled in this image — only turn on what job-exec needs
ENV CONTAINERS=1 \
    EVENTS=1 \
    EXEC=1 \
    POST=1
