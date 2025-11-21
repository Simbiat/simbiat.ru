# Based on https://frankenphp.dev/docs/docker/
FROM dunglas/frankenphp:1.10-builder-php8.5-trixie AS builder

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
    --with github.com/caddyserver/json5-adapter \
    # CrowdSec modules \
    --with github.com/hslatman/caddy-crowdsec-bouncer/crowdsec@main \
    --with github.com/hslatman/caddy-crowdsec-bouncer/http@main \
    --with github.com/hslatman/caddy-crowdsec-bouncer/layer4@main \
    --with github.com/hslatman/caddy-crowdsec-bouncer/appsec@main

FROM dunglas/frankenphp:1.10-php8.5-trixie AS runner

# Replace the official binary by the one contained your custom modules
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp

#Copy custom mime.types
COPY mime.types /etc/mime.types

# Add additional extensions
RUN install-php-extensions \
    mysqli \
    pdo_mysql \
    gd \
    zip \
    brotli \
    zstd \
    apcu \
    intl \
    opcache

# Completely custom
# Add composer
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
RUN composer self-update

# cron required for crontab, libnss3-tools is recommended for Caddy (looks like)
RUN apt-get -y update&&apt-get -y --no-install-recommends upgrade&&apt-get -y --no-install-recommends install cron libnss3-tools&&apt-get -y autoremove&&apt-get clean
# Install Git and unzip on test environment (for composer)
ARG WEB_SERVER_TEST
RUN if [ "$WEB_SERVER_TEST" = "true" ]; then \
    apt-get install -y unzip git-all; \
    fi

# Use custom caddy config
CMD ["--config", "/config/config.json5", "--adapter", "json5"]
