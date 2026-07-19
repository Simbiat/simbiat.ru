#Version needs to be updated in RUN as well
ARG MARIADB_VERSION=12.3.2
FROM mariadb:${MARIADB_VERSION}
#Copying files for initial restore. Only really needed in case no physical backup (from mariadb-backup or alternatives) is available
#COPY --chown=root ./build/DDL/*.sql /docker-entrypoint-initdb.d/
#may actually need this, since Mroonga requires a thing https://mariadb.com/kb/en/about-mroonga/

ENV MARIADB_AUTO_UPGRADE=true \
    MARIADB_ROOT_HOST=localhost \
    MARIADB_RANDOM_ROOT_PASSWORD=true \
    MARIADB_MYSQL_LOCALHOST_USER=true

# Need cron, LZ4 compression plugin for MariaDB, and 7zip for backups compression
RUN apt-get -y update && \
    apt-get -y --no-install-recommends upgrade && \
    #TODO: Update version here \
    apt-get -y --no-install-recommends install cron mariadb-plugin-provider-lz4=${MARIADB_VERSION}* 7zip && \
    apt-get -y autoremove && \
    apt-get clean

HEALTHCHECK --start-period=120s --interval=10s --timeout=5s --retries=3 CMD ["healthcheck.sh", "--defaults-file=/etc/mysql/conf.d/my.cnf", "--connect", "--innodb_initialized", "--innodb_buffer_pool_loaded", "--mariadbupgrade"]

#Copy config
COPY my.cnf /etc/mysql/conf.d/my.cnf
RUN chmod 0444 /etc/mysql/conf.d/my.cnf

#Copy cron files
COPY cron/mariadb.cron /etc/cron.d/mariadb.cron
COPY cron/*.sh /usr/local/bin/
#Setup cron
RUN chmod 0644 /etc/cron.d/mariadb.cron && \
    chmod +x /usr/local/bin/*.sh && \
    crontab /etc/cron.d/mariadb.cron

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]