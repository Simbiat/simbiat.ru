FROM ghcr.io/netresearch/ofelia:latest

COPY config.ini /etc/ofelia/config.ini

ENTRYPOINT ["ofelia", "daemon", "--config=/etc/ofelia/config.ini"]