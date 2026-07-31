FROM ghcr.io/netresearch/ofelia:latest

COPY config.ini /etc/ofelia/config.ini

ENTRYPOINT exec ofelia daemon --config=/etc/ofelia/config.ini 2>&1 | tee -a /var/log/ofelia.log