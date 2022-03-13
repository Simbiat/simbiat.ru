create table uc__groups
(
    groupid   int unsigned auto_increment
        primary key,
    groupname varchar(25) not null,
    constraint groupname
        unique (groupname)
);

