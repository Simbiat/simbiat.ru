create table cron__settings
(
    setting     varchar(10) not null comment 'Name of the setting'
        primary key,
    value       int(10)     null comment 'Value of the setting',
    description text        null comment 'Description of the setting'
);

