create table ffxiv__linkshell
(
    linkshellid varchar(40)                                     not null comment 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)'
        primary key,
    name        varchar(50)                                     not null comment 'Linkshell name',
    manual      tinyint(1) unsigned default 0                   not null comment 'Flag indicating whether entity was added manually',
    serverid    tinyint(2) unsigned                             null comment 'ID of the server Linkshell resides on',
    crossworld  tinyint(1) unsigned default 0                   not null comment 'Flag indicating whether linkshell is crossworld',
    formed      date                                            null comment 'Linkshell formation day as seen on Lodestone',
    registered  date                default current_timestamp() not null comment 'When Linkshsell was initially added to tracker',
    updated     timestamp           default current_timestamp() not null on update current_timestamp() comment 'When Linkshsell was last updated on the tracker',
    deleted     date                                            null comment 'Date when Linkshell was marked as deleted',
    communityid varchar(40)                                     null comment 'Community ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/community_finder/communityid/)',
    constraint serverid_ls
        foreign key (serverid) references ffxiv__server (serverid)
            on update cascade on delete cascade
)
    comment 'Linkshells (both crossworld and not) found on Lodestone';

create index communityid
    on ffxiv__linkshell (communityid);

create index crossworld
    on ffxiv__linkshell (crossworld);

create index deleted
    on ffxiv__linkshell (deleted);

create fulltext index name
    on ffxiv__linkshell (name);

create index name_order
    on ffxiv__linkshell (name);

create index registered
    on ffxiv__linkshell (registered);

create index updated
    on ffxiv__linkshell (updated);

