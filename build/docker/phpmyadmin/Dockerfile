FROM phpmyadmin:5.2.3-apache

ENV PMA_ARBITRARY=0 \
    PMA_PMADB=phpmyadmin \
    PMA_CONTROLUSER=phpmyadmin \
    PMA_UPLOADDIR="/etc/phpmyadmin/upload" \
    PMA_SAVEDIR="/etc/phpmyadmin/save"

HEALTHCHECK --interval=5m --timeout=10s --start-period=30s --retries=3 \
            CMD curl -sf http://localhost/index.php >/dev/null || exit 1

RUN secret="$(tr -dc 'a-zA-Z0-9~!@#$%^&*_()+}{?></\";.,[]=-' < /dev/urandom | fold -w 32 | head -n 1)" && \
    printf "<?php\n\$cfg['blowfish_secret'] = '%s';\n" "$secret" \
      > /etc/phpmyadmin/config.secret.inc.php && \
    chgrp www-data /etc/phpmyadmin/config.secret.inc.php

ENTRYPOINT ["/docker-entrypoint.sh", "apache2-foreground"]