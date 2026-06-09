FROM crowdsecurity/crowdsec:v1.7.8-debian

#When CRS is updated, check for potential changes in 02-crs-setup.conf as well
ARG CRS_RELEASE="4.27.0"

#Copy configs
COPY yaml/acquis.yaml /etc/crowdsec/acquis.yaml
COPY yaml/config.yaml.local /etc/crowdsec/config.yaml.local
COPY yaml/online_api_credentials.yaml /etc/crowdsec/online_api_credentials.yaml
COPY yaml/acquis/*.yaml /etc/crowdsec/acquis.d/
COPY yaml/appsec/simbiat-appsec.yaml /etc/crowdsec/appsec-configs/simbiat-appsec.yaml
COPY yaml/appsec/simbiat-seclang.yaml /etc/crowdsec/appsec-rules/simbiat-seclang.yaml
COPY CoreRuleSet/* /etc/crowdsec/CoreRuleSet/

RUN apt-get -y update&&apt-get -y --no-install-recommends upgrade&&apt-get -y --no-install-recommends install curl gpg dirmngr gpg-agent&&apt-get -y autoremove&&apt-get clean && \
    curl -sSL https://github.com/coreruleset/coreruleset/releases/download/v${CRS_RELEASE}/coreruleset-${CRS_RELEASE}-minimal.tar.gz -o v${CRS_RELEASE}-minimal.tar.gz && \
    curl -sSL https://github.com/coreruleset/coreruleset/releases/download/v${CRS_RELEASE}/coreruleset-${CRS_RELEASE}-minimal.tar.gz.asc -o coreruleset-${CRS_RELEASE}-minimal.tar.gz.asc && \
    gpg --fetch-key https://coreruleset.org/security.asc && \
    gpg --verify coreruleset-${CRS_RELEASE}-minimal.tar.gz.asc v${CRS_RELEASE}-minimal.tar.gz && \
    mkdir /var/lib/OWASP/ && \
    tar -zxf v${CRS_RELEASE}-minimal.tar.gz --strip-components=1 -C /var/lib/OWASP/ && \
    rm -f v${CRS_RELEASE}-minimal.tar.gz coreruleset-${CRS_RELEASE}-minimal.tar.gz.asc && \
    mv -v /var/lib/OWASP/crs-setup.conf.example /var/lib/OWASP/crs-setup.conf && \
    apt-get remove  -y --purge curl gpg dirmngr gpg-agent&&apt-get clean