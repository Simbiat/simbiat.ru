FROM oven/bun:latest

# Default values
ARG PUID=1000
ARG PGID=1000

RUN groupmod -o -g "${PGID}" bun && usermod -o -u "${PUID}" -g "${PGID}" bun

USER bun
