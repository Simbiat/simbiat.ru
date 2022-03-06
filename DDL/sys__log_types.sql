create table sys__log_types
(
    typeid tinyint(2) unsigned auto_increment comment 'Type ID'
        primary key,
    name   varchar(100) not null comment 'Name of the type',
    constraint name
        unique (name)
)
    comment 'Definitions of types of logs';

