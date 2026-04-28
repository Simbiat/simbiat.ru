# Based on https://frankenphp.dev/docs/docker/
FROM dunglas/frankenphp:1.12.2-builder-php8.5.5-trixie AS builder

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

FROM dunglas/frankenphp:1.12.2-php8.5.5-trixie AS runner

# Replace the official binary by the one contained your custom modules
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp

#Copy custom mime.types
COPY mime.types /etc/mime.types
#Copy php.ini files
COPY php.ini.dev $PHP_INI_DIR/php.ini.dev
COPY php.ini.prod $PHP_INI_DIR/php.ini.prod
#Copy PHP tools configs
COPY .php-cs-fixer.php $PHP_INI_DIR/.php-cs-fixer.php
COPY phpstan.neon $PHP_INI_DIR/phpstan.neon
COPY phpunit.xml $PHP_INI_DIR/phpunit.xml
COPY psalm.xml $PHP_INI_DIR/psalm.xml
#Copy Caddy config
COPY caddy.json5 $PHP_INI_DIR/caddy.json5
#Copy cron files
COPY cron/php.cron /etc/cron.d/php.cron
COPY cron/*.sh /usr/local/bin/

# Add additional extensions
ARG WEB_SERVER_TEST
RUN if [ "$WEB_SERVER_TEST" = "true" ]; then \
      install-php-extensions \
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
          pcntl \
          igbinary \
          xdebug; \
    else \
      install-php-extensions \
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
          uuid; \
    fi

# Completely custom
# Add composer
ENV COMPOSER_HOME=/root/.composer
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
RUN composer self-update

# cron required for crontab, libnss3-tools is recommended for Caddy (looks like)
RUN apt-get -y update&&apt-get -y --no-install-recommends upgrade&&apt-get -y --no-install-recommends install cron libnss3-tools&&apt-get -y autoremove&&apt-get clean
RUN if [ "$WEB_SERVER_TEST" = "true" ]; then \
      # Install Git and unzip for composer \
      apt-get install -y unzip git-all; \
      # Install PHP tools globally \
      composer global require "roave/security-advisories:dev-latest" \
                              "friendsofphp/php-cs-fixer:@stable" \
                              "phpstan/phpstan:@stable" \
                              "phpstan/phpstan-strict-rules:@stable" \
                              "phpstan/phpstan-deprecation-rules:@stable" \
                              "phpstan/phpstan-phpunit:@stable" \
                              "phpstan/phpstan-symfony:@stable" \
                              "vimeo/psalm:@stable" \
                              "psalm/plugin-phpunit:@stable" \
                              "psalm/plugin-symfony:@stable" \
                              "phpunit/phpunit:@stable"; \
    fi
#Copy appropriate php.ini file
RUN if [ "$WEB_SERVER_TEST" = "true" ]; then \
      cp "$PHP_INI_DIR/php.ini.dev" "$PHP_INI_DIR/php.ini"; \
    else \
      cp "$PHP_INI_DIR/php.ini.prod" "$PHP_INI_DIR/php.ini"; \
    fi
#Setup cron
RUN chmod 0644 /etc/cron.d/php.cron && \
    chmod +x /usr/local/bin/*.sh && \
    crontab /etc/cron.d/php.cron

# Create some temp directories
RUN mkdir -p /tmp/upload/
RUN mkdir -p /tmp/sessions/
RUN mkdir -p /tmp/soap/
RUN mkdir -p /tmp/opcache/
RUN mkdir -p /tmp/upload/

# Use custom caddy config
CMD ["--config", "/config/config.json5", "--adapter", "json5"]
