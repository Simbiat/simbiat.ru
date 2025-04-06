#Version needs to be updated in RUN as well
FROM mariadb:11.7.2-noble
COPY --chown=root ./build/DDL/00-create_databases.sql /docker-entrypoint-initdb.d/

# Need cron, LZ4 compression plugin for MariaDB, and 7zip for backups compression
RUN apt-get -y update&&apt-get -y --no-install-recommends upgrade&&apt-get -y --no-install-recommends install cron mariadb-plugin-provider-lz4=1:11.7.2+maria~ubu2404 7zip&&apt-get -y autoremove&&apt-get clean