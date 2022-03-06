create table sys__settings
(
    setting     varchar(100)  not null
        primary key,
    value       varchar(5000) null,
    description text          null
);

