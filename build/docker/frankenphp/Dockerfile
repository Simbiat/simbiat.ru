# Based on https://frankenphp.dev/docs/docker/
FROM dunglas/frankenphp:1.12.7-builder-php8.5.9-trixie AS builder

# Copy xcaddy in the builder image
COPY --from=caddy:builder /usr/bin/xcaddy /usr/bin/xcaddy

# CGO must be enabled to build FrankenPHP
RUN CGO_ENABLED=1 \
    XCADDY_SETCAP=1 \
    XCADDY_GO_BUILD_FLAGS="-ldflags='-w -s' -tags=nobadger,nomysql,nopgx" \
    CGO_CFLAGS=$(php-config --includes) \
    CGO_LDFLAGS="$(php-config --ldflags) $(php-config --libs)" \
    xcaddy build \
    --output /usr/local/bin/frankenphp \
    --with github.com/dunglas/frankenphp=./ \
    --with github.com/dunglas/frankenphp/caddy=./caddy/ \
    # Mercure and Vulcain are included in the official build, but feel free to remove them \
    --with github.com/dunglas/caddy-cbrotli \
    --with github.com/dunglas/mercure/caddy \
    --with github.com/dunglas/vulcain/caddy \
    # Add extra Caddy modules here \
    # CrowdSec modules \
    --with github.com/mholt/caddy-ratelimit@master \
    --with github.com/hslatman/caddy-crowdsec-bouncer/crowdsec@main \
    --with github.com/hslatman/caddy-crowdsec-bouncer/http@main \
    --with github.com/hslatman/caddy-crowdsec-bouncer/layer4@main \
    --with github.com/hslatman/caddy-crowdsec-bouncer/appsec@main

FROM dunglas/frankenphp:1.12.7-php8.5.9-trixie AS base

# Replace the official binary by the one containing custom modules
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp

# Extensions common to every environment
RUN install-php-extensions \
    mysqli \
    pdo_mysql \
    gd \
    zip \
    brotli \
    zstd \
    apcu \
    intl \
    bcmath \
    gmp \
    maxminddb \
    yaml \
    uuid \
    ctype \
    iconv \
    mbstring \
    curl \
    fileinfo \
    zlib \
    dom \
    libxml \
    openssl \
    pcntl \
    igbinary \
    xdebug \
    pcov \
    opcache

# Completely custom

# libnss3-tools is recommended for Caddy (looks like), unzip and git are requried for Composer
RUN apt-get -y update&&apt-get -y --no-install-recommends upgrade&&apt-get -y --no-install-recommends install libnss3-tools unzip git&&apt-get -y autoremove&&apt-get clean

# Add composer
ENV COMPOSER_HOME=/composer
ENV COMPOSER_CACHE_DIR=/tmp/composer_cache
ENV PATH="${COMPOSER_HOME}/vendor/bin:/app/vendor/bin:/app/bin:${PATH}"
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
RUN composer self-update

# Symfony CLI
#TODO: Update version when Symfony CLI has a new release
COPY --link --from=ghcr.io/symfony-cli/symfony-cli:v5.20 /usr/local/bin/symfony /usr/local/bin/symfony
RUN mkdir -p /etc/bash_completion.d && symfony completion bash | tee /etc/bash_completion.d/symfony

# Default values
ARG PUID=1000
ARG PGID=1000

# Create custom user with UID and GID matching host
RUN groupadd -g "${PGID}"  frankenphp && useradd -u "${PUID}"  -g frankenphp -m -s /bin/bash frankenphp

RUN mkdir -p /usr/local/share/ca-bundle-backup && \
    cp -a /etc/ssl/certs/. /usr/local/share/ca-bundle-backup/

RUN mkdir -p /config && chown -R ${PUID}:${PGID} /config
RUN mkdir -p /data && chown -R ${PUID}:${PGID} /data

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 755 /usr/local/bin/entrypoint.sh

RUN mkdir /healthcheck
COPY healthcheck.php /healthcheck/healthcheck.php

HEALTHCHECK --start-period=30s --interval=10s --timeout=5s --retries=3 CMD ["curl", "-f", "http://localhost:2026"]

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

USER frankenphp
