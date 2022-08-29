create table ffxiv__pvpteam
(
    pvpteamid    varchar(40)                                     not null comment 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)'
        primary key,
    name         varchar(50)                                     not null comment 'PvP Team name',
    manual       tinyint(1) unsigned default 0                   not null comment 'Flag indicating whether entity was added manually',
    datacenterid tinyint(2) unsigned                             null comment 'ID of the server PvP Team resides on',
    formed       date                                            null comment 'PvP Team formation day as seen on Lodestone',
    registered   date                default current_timestamp() not null comment 'When PvP Team was initially added to tracker',
    updated      timestamp           default current_timestamp() not null on update current_timestamp() comment 'When PvP Team was last updated on the tracker',
    deleted      date                                            null comment 'Date when PvP Team was marked as deleted',
    communityid  varchar(40)                                     null comment 'Community ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/community_finder/communityid/)',
    crest        char(64)                                        null comment 'Name (hash) of image representing merged crest for the team (generated on each company update from 1 to 3 images on Lodestone)',
    crest_part_1 varchar(100)                                    null comment 'Link to 1st part of the crest',
    crest_part_2 varchar(100)                                    null comment 'Link to 2nd part of the crest',
    crest_part_3 varchar(100)                                    null comment 'Link to 3rd part of the crest',
    constraint pvp_dcid
        foreign key (datacenterid) references ffxiv__server (serverid)
            on update cascade on delete cascade
)
    comment 'PvP Teams found on Lodestone';

create index communityid
    on ffxiv__pvpteam (communityid);

create index deleted
    on ffxiv__pvpteam (deleted);

create fulltext index name
    on ffxiv__pvpteam (name);

create index name_order
    on ffxiv__pvpteam (name);

create index registered
    on ffxiv__pvpteam (registered);

create index updated
    on ffxiv__pvpteam (updated desc);
