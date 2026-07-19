#!/bin/sh
set -e

# Need to link GeoIP files
ln -sf /geoip/GeoLite2-ASN.mmdb /var/lib/crowdsec/data/GeoLite2-ASN.mmdb &&
ln -sf /geoip/GeoLite2-City.mmdb /var/lib/crowdsec/data/GeoLite2-City.mmdb &&

# Need to ensure that CRS files are up-to-date every time, so we remove them first
rm -f /var/lib/crowdsec/data/REQUEST-9*.conf \
      /var/lib/crowdsec/data/RESPONSE-9*.conf \
      /var/lib/crowdsec/data/iis-errors.data \
      /var/lib/crowdsec/data/java-classes.data \
      /var/lib/crowdsec/data/java-errors.data \
      /var/lib/crowdsec/data/lfi-os-files.data \
      /var/lib/crowdsec/data/php-config-directives.data \
      /var/lib/crowdsec/data/php-errors.data \
      /var/lib/crowdsec/data/php-errors-pl2.data \
      /var/lib/crowdsec/data/php-function-names-933150.data \
      /var/lib/crowdsec/data/php-function-names-933151.data \
      /var/lib/crowdsec/data/php-variables.data \
      /var/lib/crowdsec/data/restricted-files.data \
      /var/lib/crowdsec/data/restricted-upload.data \
      /var/lib/crowdsec/data/scanners-user-agents.data \
      /var/lib/crowdsec/data/sql-errors.data \
      /var/lib/crowdsec/data/ssrf.data \
      /var/lib/crowdsec/data/unix-shell.data \
      /var/lib/crowdsec/data/web-shells-asp.data \
      /var/lib/crowdsec/data/web-shells-php.data \
      /var/lib/crowdsec/data/windows-powershell-commands.data ;
cp -r /var/lib/OWASP/rules/* /var/lib/crowdsec/data/ &&
mkdir -p /var/lib/crowdsec/data/CoreRuleSet/ &&
cp -r /opt/CoreRuleSet/* /var/lib/crowdsec/data/CoreRuleSet/ &&

# CrowdSec's native entrypoint symlinks any file from `staging`, if it's missing. This will break on restart due to `read_only: true`
if [ -d /staging/var/lib/crowdsec/data ]; then
    cp -rn /staging/var/lib/crowdsec/data/. /var/lib/crowdsec/data/
    rm -f /var/lib/crowdsec/data/crowdsec.db*
fi

exec /docker_start.sh