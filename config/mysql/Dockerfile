FROM mariadb:11.4.2-noble

# Need cron, LZ4 compression plugin for MariaDB, and 7zip for backups compression
RUN apt-get -y update&&apt-get -y upgrade&&apt-get -y install cron mariadb-plugin-provider-lz4 7zip&&apt-get -y autoremove
