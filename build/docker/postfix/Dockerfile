# TODO: Use an actual version once the release updates to include healthcheck
FROM mwader/postfix-relay:latest

# Default values
ARG PUID=1000
ARG PGID=1000

# This is not necessarily required, but used for consistency
RUN groupadd -g "${PGID}"  postfix_local && useradd -u "${PUID}"  -g postfix_local -m -s /bin/bash postfix_local

# Required to have `mail.log` be written with the same GID/UID as stuff from other containers
RUN printf '$FileOwnerNum %s\n$FileGroupNum %s\n$FileCreateMode 0640\n' "${PUID}" "${PGID}" \
    > /etc/rsyslog.d/00-privdrop.conf

ENV POSTFIX_smtp_sasl_security_options="noanonymous" \
    POSTFIX_smtp_tls_security_level="encrypt" \
    POSTFIX_myhostname="smtp.internal" \
    POSTFIX_inet_protocols="all" \
    POSTFIX_smtp_sasl_auth_enable="yes" \
    POSTFIX_smtp_sasl_password_maps="hash:/etc/postfix/sasl_passwd" \
    RSYSLOG_LOG_TO_FILE="yes" \
    RSYSLOG_TIMESTAMP="yes"