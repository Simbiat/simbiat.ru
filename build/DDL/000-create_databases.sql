/*phpMyAdmin can create the database itself, but creating it separately for convenience. Tables are created through UI, they are not critical, so not generating them separately.*/
CREATE DATABASE IF NOT EXISTS phpmyadmin;
/*The main application does not create its own database. Tables are created by following SQL files, and some data is added by them as well (999-*.sql files).*/
CREATE DATABASE IF NOT EXISTS simbiatr_simbiat;
