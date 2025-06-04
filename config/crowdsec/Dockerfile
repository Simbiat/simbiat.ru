FROM crowdsecurity/crowdsec:v1.6.8-debian

ARG CRS_RELEASE="4.15.0"

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