#Version needs to be updated in RUN as well
FROM mariadb:11.7.2-noble
#Copying files for initial restore. Only really needed in case no physical backup (from mariadb-backup or alternatives) is available
#COPY --chown=root ./build/DDL/*.sql /docker-entrypoint-initdb.d/
#may actually need this, since Mroonga requires a thing https://mariadb.com/kb/en/about-mroonga/

# Need cron, LZ4 compression plugin for MariaDB, and 7zip for backups compression
RUN apt-get -y update&&apt-get -y --no-install-recommends upgrade&&apt-get -y --no-install-recommends install cron mariadb-plugin-provider-lz4=1:11.7.2+maria~ubu2404 7zip&&apt-get -y autoremove&&apt-get clean