FROM phpmyadmin:5.2.3-apache

ENV PMA_ARBITRARY=0 \
    PMA_PMADB=phpmyadmin \
    PMA_CONTROLUSER=phpmyadmin \
    PMA_UPLOADDIR="/etc/phpmyadmin/upload" \
    PMA_SAVEDIR="/etc/phpmyadmin/save"

RUN secret="$(tr -dc 'a-zA-Z0-9~!@#$%^&*_()+}{?></\";.,[]=-' < /dev/urandom | fold -w 32 | head -n 1)" && \
    printf "<?php\n\$cfg['blowfish_secret'] = '%s';\n" "$secret" \
      > /etc/phpmyadmin/config.secret.inc.php && \
    chgrp www-data /etc/phpmyadmin/config.secret.inc.php

COPY config.inc.php /etc/phpmyadmin/config.user.inc.php

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]