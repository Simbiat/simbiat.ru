create table ffxiv__freecompany
(
    freecompanyid  varchar(20)                                     not null comment 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)'
        primary key,
    name           varchar(50)                                     not null comment 'Free Company name',
    manual         tinyint(1) unsigned default 0                   not null comment 'Flag indicating whether entity was added manually',
    serverid       tinyint(2) unsigned                             null comment 'ID of the server Free Company resides on',
    grandcompanyid tinyint(2) unsigned                             null comment 'ID of Grand Company affiliated with the Free Company',
    tag            varchar(10)                                     null comment 'Short name of Free Company',
    formed         date                default current_timestamp() not null comment 'Free Company formation day as seen on Lodestone',
    registered     date                default current_timestamp() not null comment 'When Free Company was initially added to tracker',
    updated        timestamp           default current_timestamp() not null on update current_timestamp() comment 'When Free Company was last updated on the tracker',
    deleted        date                                            null comment 'Date when Free Company was marked as deleted',
    crest          char(64)                                        null comment 'Name (hash) of image representing merged crest for the company (generated on each company update from 1 to 3 images on Lodestone)',
    `rank`         tinyint(2) unsigned default 1                   not null comment 'Company level',
    slogan         text                                            null comment 'Public message shown on company board as seen on Lodestone',
    activeid       tinyint(1) unsigned                             null comment 'ID of active time as registered on tracker',
    recruitment    tinyint(1) unsigned default 0                   not null comment 'Whether company is recruiting or not',
    communityid    varchar(40)                                     null comment 'Community ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/community_finder/communityid/)',
    estate_zone    text                                            null comment 'Name of estate',
    estateid       smallint unsigned                               null comment 'Estate ID as registered by the tracker',
    estate_message text                                            null comment 'Greeting on estate board as shown on Lodestone',
    `Role-playing` tinyint(1) unsigned default 0                   not null comment 'Whether company participates in role-playing',
    Leveling       tinyint(1) unsigned default 0                   not null comment 'Whether company participates in leveling',
    Casual         tinyint(1) unsigned default 0                   not null comment 'Whether company participates in casual activities',
    Hardcore       tinyint(1) unsigned default 0                   not null comment 'Whether company participates in hardcore activities',
    Dungeons       tinyint(1) unsigned default 0                   not null comment 'Whether company participates in dungeons',
    Guildhests     tinyint(1) unsigned default 0                   not null comment 'Whether company participates in guildhests',
    Trials         tinyint(1) unsigned default 0                   not null comment 'Whether company participates in trials',
    Raids          tinyint(1) unsigned default 0                   not null comment 'Whether company participates in raids',
    PvP            tinyint(1) unsigned default 0                   not null comment 'Whether company participates in PvP',
    Tank           tinyint(1) unsigned default 0                   not null comment 'Whether company is looking for tanks',
    Healer         tinyint(1) unsigned default 0                   not null comment 'Whether company is looking for healers',
    DPS            tinyint(1) unsigned default 0                   not null comment 'Whether company is looking for DPSs',
    Crafter        tinyint(1) unsigned default 0                   not null comment 'Whether company is looking for crafters',
    Gatherer       tinyint(1) unsigned default 0                   not null comment 'Whether company is looking for gatherers',
    constraint activeid2
        foreign key (activeid) references ffxiv__timeactive (activeid)
            on update set null on delete set null,
    constraint estateid
        foreign key (estateid) references ffxiv__estate (estateid)
            on update set null on delete set null,
    constraint grandcompanyid
        foreign key (grandcompanyid) references ffxiv__grandcompany (gcId)
            on update cascade on delete set null,
    constraint serverid_fc
        foreign key (serverid) references ffxiv__server (serverid)
            on update cascade on delete cascade
)
    comment 'Free Companies found on Lodestone';

create index activeid
    on ffxiv__freecompany (activeid);

create index communityid
    on ffxiv__freecompany (communityid);

create index deleted
    on ffxiv__freecompany (deleted);

create fulltext index estate_message
    on ffxiv__freecompany (estate_message);

create fulltext index estate_zone
    on ffxiv__freecompany (estate_zone);

create fulltext index name
    on ffxiv__freecompany (name);

create index name_order
    on ffxiv__freecompany (name);

create index registered
    on ffxiv__freecompany (registered);

create fulltext index slogan
    on ffxiv__freecompany (slogan);

create fulltext index tag
    on ffxiv__freecompany (tag);

create index updated
    on ffxiv__freecompany (updated desc);

