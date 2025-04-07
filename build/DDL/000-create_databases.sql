/*CrowdSec requires database to be created manually for it to work. Tables and their contents are handled by CrowdSec itself. In fact, if restoring from Physical backup, tables need to be removed.*/
CREATE DATABASE IF NOT EXISTS crowdsec;
/*phpMyAdmin can create database itself, but creating it separately for convenience. Tables are created through UI, they are not critical, so not generating them separately.*/
CREATE DATABASE IF NOT EXISTS phpmyadmin;
/*Main application does not create its own database. Tables are created by following SQL files, and some data is added by them as well (999-*.sql files).*/
CREATE DATABASE IF NOT EXISTS simbiatr_simbiat;