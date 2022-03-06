create table ban__ips
(
    ip     varchar(45)                      not null comment 'Banned IP'
        primary key,
    added  date default current_timestamp() not null comment 'When IP was banned',
    reason text                             null comment 'Reason for the ban'
)
    comment 'Banned IPs';

create index added
    on ban__ips (added);

