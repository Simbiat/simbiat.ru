FROM crowdsecurity/crowdsec:v1.7.8-debian

#TODO: When CRS is updated, check for potential changes in 02-crs-setup.conf as well
ARG CRS_RELEASE="4.29.0"
#TODO: When CRS is updated, check for potential changes in 02-crs-setup.conf as well

# Default values
ARG PUID=1000
ARG PGID=1000

# Create custom user with UID and GID matching host
RUN groupadd -g "${PGID}"  crowdsec && useradd -u "${PUID}"  -g crowdsec -m -s /bin/bash crowdsec

ENV TZ=UTC \
    ENABLE_CONSOLE_MANAGEMENT=true \
    ENABLE_CONSOLE_ALL=true \
    COLLECTIONS="crowdsecurity/caddy crowdsecurity/whitelist-good-actors crowdsecurity/http-cve crowdsecurity/http-dos crowdsecurity/base-http-scenarios crowdsecurity/http-extended-context crowdsecurity/appsec-generic-rules crowdsecurity/appsec-virtual-patching crowdsecurity/appsec-wordpress crowdsecurity/wordpress crowdsecurity/discord-crawler-whitelist crowdsecurity/linux crowdsecurity/linux-lpe crowdsecurity/iptables crowdsecurity/mariadb crowdsecurity/sshd barnoux/caddy-coraza openappsec/openappsec" \
    PARSERS="crowdsecurity/appsec-logs" \
    SCENARIOS="crowdsecurity/appsec-native crowdsecurity/crowdsec-appsec-outofband"

# Install CRS
RUN mkdir /var/lib/OWASP/
RUN apt-get -y update && \
    apt-get -y --no-install-recommends upgrade && \
    apt-get -y --no-install-recommends install curl gpg dirmngr gpg-agent && \
    curl -sSL https://github.com/coreruleset/coreruleset/releases/download/v${CRS_RELEASE}/coreruleset-${CRS_RELEASE}-minimal.tar.gz -o v${CRS_RELEASE}-minimal.tar.gz && \
    curl -sSL https://github.com/coreruleset/coreruleset/releases/download/v${CRS_RELEASE}/coreruleset-${CRS_RELEASE}-minimal.tar.gz.asc -o coreruleset-${CRS_RELEASE}-minimal.tar.gz.asc && \
    gpg --fetch-key https://coreruleset.org/security.asc && \
    gpg --verify coreruleset-${CRS_RELEASE}-minimal.tar.gz.asc v${CRS_RELEASE}-minimal.tar.gz && \
    tar -zxf v${CRS_RELEASE}-minimal.tar.gz --strip-components=1 -C /var/lib/OWASP/ && \
    rm -f v${CRS_RELEASE}-minimal.tar.gz coreruleset-${CRS_RELEASE}-minimal.tar.gz.asc && \
    mv -v /var/lib/OWASP/crs-setup.conf.example /var/lib/OWASP/crs-setup.conf && \
    apt-get remove -y --purge curl gpg dirmngr gpg-agent && \
    apt-get -y autoremove && \
    apt-get clean

HEALTHCHECK --start-period=120s --interval=10s --timeout=5s --retries=3 CMD ["cscli", "lapi", "status"]

# Copy entrypoint scripts
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 755 /usr/local/bin/entrypoint.sh

# Own the files
RUN chown -R crowdsec:crowdsec /var/lib/crowdsec /staging /usr/local/lib/crowdsec/plugins /var/lib/OWASP/rules

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

USER crowdsec
