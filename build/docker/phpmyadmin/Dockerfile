FROM phpmyadmin:5.2.3-apache

ENV PMA_ARBITRARY=0 \
    PMA_PMADB=phpmyadmin \
    PMA_CONTROLUSER=phpmyadmin \
    PMA_UPLOADDIR="/etc/phpmyadmin/upload" \
    PMA_SAVEDIR="/etc/phpmyadmin/save"

COPY config.inc.php /etc/phpmyadmin/config.user.inc.php

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]