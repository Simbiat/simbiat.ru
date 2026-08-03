# Based on https://frankenphp.dev/docs/docker/
FROM dunglas/frankenphp:1.12.6-builder-php8.5.8-trixie AS builder

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

FROM dunglas/frankenphp:1.12.6-php8.5.8-trixie AS base

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
    uuid

# Completely custom

# libnss3-tools is recommended for Caddy (looks like)
RUN apt-get -y update&&apt-get -y --no-install-recommends upgrade&&apt-get -y --no-install-recommends install libnss3-tools&&apt-get -y autoremove&&apt-get clean

# Default values
ARG PUID=1000
ARG PGID=1000

# Create custom user with UID and GID matching host
RUN groupadd -g "${PGID}"  frankenphp && useradd -u "${PUID}"  -g frankenphp -m -s /bin/bash frankenphp

RUN mkdir -p /usr/local/share/ca-bundle-backup && \
    cp -a /etc/ssl/certs/. /usr/local/share/ca-bundle-backup/

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 755 /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# ---------------------------------------------------------------------------
# PROD target: nothing beyond base. No git, no unzip, no composer global
# tooling, no xdebug ever get built into this image at all.
# ---------------------------------------------------------------------------
FROM base AS PROD

USER frankenphp

# ---------------------------------------------------------------------------
# DEV target: adds the extra extensions and QA tooling on top of base.
# ---------------------------------------------------------------------------
FROM base AS DEV

# Add composer
ENV COMPOSER_HOME=/composer
ENV COMPOSER_CACHE_DIR=/tmp/composer_cache
ENV PATH="${COMPOSER_HOME}/vendor/bin:${PATH}"
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
RUN composer self-update

# Git and unzip needed by composer for VCS/zip package installs
RUN apt-get install -y --no-install-recommends unzip git

# Add PHP extensions for DEV
RUN install-php-extensions \
    pcntl \
    igbinary \
    xdebug

RUN composer global config --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true && \
    composer global require "roave/security-advisories:dev-latest" \
                            "phpstan/phpstan:@stable" \
                            "phpstan/phpstan-strict-rules:@stable" \
                            "phpstan/phpstan-deprecation-rules:@stable" \
                            "phpstan/phpstan-phpunit:@stable" \
                            "phpstan/phpstan-symfony:@stable" \
                            "vimeo/psalm:@stable" \
                            "psalm/plugin-phpunit:@stable" \
                            "psalm/plugin-symfony:@stable" \
                            "phpunit/phpunit:@stable" \
                            "squizlabs/php_codesniffer:@stable" \
                            "dealerdirect/phpcodesniffer-composer-installer:@stable" \
                            "escapestudios/symfony2-coding-standard:@stable" \
                            "slevomat/coding-standard:@stable" \
                            "phpcsstandards/phpcsextra:@stable" \
                            "phpcompatibility/php-compatibility:@stable"

USER frankenphp