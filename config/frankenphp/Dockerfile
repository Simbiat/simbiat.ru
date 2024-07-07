# Based on https://frankenphp.dev/docs/docker/
FROM dunglas/frankenphp:1.2.1-builder-php8.3.9-bookworm AS builder

# Copy xcaddy in the builder image
COPY --from=caddy:builder /usr/bin/xcaddy /usr/bin/xcaddy

# CGO must be enabled to build FrankenPHP
ENV CGO_ENABLED=1 XCADDY_SETCAP=1 XCADDY_GO_BUILD_FLAGS="-ldflags '-w -s'"
RUN xcaddy build \
    --output /usr/local/bin/frankenphp \
    --with github.com/dunglas/frankenphp=./ \
    --with github.com/dunglas/frankenphp/caddy=./caddy/ \
    # Mercure and Vulcain are included in the official build, but feel free to remove them
    --with github.com/dunglas/caddy-cbrotli \
    --with github.com/dunglas/mercure/caddy \
    --with github.com/dunglas/vulcain/caddy \
    # Add extra Caddy modules here
    --with github.com/caddyserver/json5-adapter \
    --with github.com/corazawaf/coraza-caddy/v2 \
    --with github.com/hslatman/caddy-crowdsec-bouncer/http

FROM dunglas/frankenphp:1.2.1-php8.3.9-bookworm AS runner

# Replace the official binary by the one contained your custom modules
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp

# Add additional extensions
RUN install-php-extensions \
    mysqli \
    pdo_mysql \
    gd \
    zip \
    brotli \
    zstd \
    apcu \
    intl

# Completely custom
# Add composer
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
RUN composer self-update

# cron required for crontab, libnss3-tools is recommended for Caddy (looks like)
RUN apt-get -y update&&apt-get -y upgrade&&apt-get -y install cron libnss3-tools&&apt-get -y autoremove
# Install Git and unzip on test environment (for composer)
ARG WEB_SERVER_TEST
RUN if [ "$WEB_SERVER_TEST" = "true" ]; then \
    apt-get install -y unzip git-all; \
    fi

# Use custom caddy config
CMD ["--config", "/config/config.json5", "--adapter", "json5"]
