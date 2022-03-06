create table bic__settings
(
    setting     varchar(10)  not null comment 'Name of the setting'
        primary key,
    value       varchar(100) not null comment 'Value of the setting',
    description varchar(250) null comment 'Optional description of the setting'
)
    comment 'List of settings for BIC Tracker';

