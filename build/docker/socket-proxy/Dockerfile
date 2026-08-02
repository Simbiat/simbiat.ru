FROM tecnativa/docker-socket-proxy:latest

HEALTHCHECK --interval=5m --timeout=5s --start-period=15s --retries=3 \
        CMD test -S /var/run/docker.sock && pgrep haproxy >/dev/null || exit 1