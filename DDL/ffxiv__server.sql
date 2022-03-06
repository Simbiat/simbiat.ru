create table ffxiv__server
(
    serverid   tinyint(2) unsigned auto_increment comment 'Server ID as registered by the tracker'
        primary key,
    server     varchar(20) not null comment 'Server name',
    datacenter varchar(10) not null comment 'Data center name',
    constraint server
        unique (server, datacenter)
)
    comment 'List of servers/data centers';

