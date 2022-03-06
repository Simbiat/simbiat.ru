create table uc__groups
(
    groupid   int unsigned auto_increment
        primary key,
    groupname varchar(190) not null,
    constraint groupname
        unique (groupname)
);

