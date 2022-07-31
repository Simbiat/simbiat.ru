create table ffxiv__character
(
    characterid   int unsigned                                     not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)'
        primary key,
    userid        int unsigned                                     null comment 'ID of the user, that is linked to the character',
    serverid      tinyint(2) unsigned                              null comment 'ID of the server character resides on',
    name          varchar(50)                                      not null comment 'Character''s name',
    manual        tinyint(1) unsigned  default 0                   not null comment 'Flag indicating whether entity was added manually',
    avatar        varchar(66)                                      not null comment 'ID portion of the link to character avatar (requires adding of ''l0_640x873.jpg'' or ''c0_96x96.jpg'' to the end of the field and ''https://img2.finalfantasyxiv.com/f/'' to the beginning to be turned into actual image link)',
    registered    date                 default current_timestamp() not null comment 'When character was initially added to tracker',
    updated       timestamp            default current_timestamp() not null on update current_timestamp() comment 'When character was last updated on the tracker',
    deleted       date                                             null comment 'Date when character was marked as deleted',
    enemyid       int unsigned                                     null comment 'ID of an enemy that "killed" the character',
    biography     text                                             null comment 'Text from "Character profile" section on Lodestone',
    titleid       smallint unsigned                                null comment 'ID of achievement title currently being used by character',
    clanid        tinyint(2) unsigned                              null comment 'Clan ID identifying both clan and race of the character',
    genderid      tinyint(1) unsigned  default 1                   not null comment '0 for female and 1 for male',
    namedayid     smallint(3) unsigned default 1                   not null comment 'ID of nameday (birthday) of character',
    guardianid    tinyint(2) unsigned  default 4                   not null comment 'ID of Guardian chosen by character',
    cityid        tinyint(1) unsigned  default 5                   not null comment 'ID of character''s starting city',
    gcrankid      tinyint(2) unsigned                              null comment 'ID of character''s Grand Company''s affiliation and current rank there',
    pvp_matches   int(10)              default 0                   null comment 'Number of PvP matches character participated in',
    Alchemist     tinyint unsigned     default 0                   not null comment 'Level of Alchemist job',
    Armorer       tinyint unsigned     default 0                   not null comment 'Level of Armorer job',
    Astrologian   tinyint unsigned     default 0                   not null comment 'Level of Astrologian job',
    Bard          tinyint unsigned     default 0                   not null comment 'Level of Bard job',
    BlackMage     tinyint unsigned     default 0                   not null comment 'Level of Black Mage job',
    Blacksmith    tinyint unsigned     default 0                   not null comment 'Level of Blacksmith job',
    BlueMage      tinyint unsigned     default 0                   not null comment 'Level of Blue Mage job',
    Botanist      tinyint unsigned     default 0                   not null comment 'Level of Botanist job',
    Carpenter     tinyint unsigned     default 0                   not null comment 'Level of Carpenter job',
    Culinarian    tinyint unsigned     default 0                   not null comment 'Level of Culinarian job',
    Dancer        tinyint unsigned     default 0                   not null comment 'Level of Dancer job',
    DarkKnight    tinyint unsigned     default 0                   not null comment 'Level of Dark Knight job',
    Dragoon       tinyint unsigned     default 0                   not null comment 'Level of Dragoon job',
    Fisher        tinyint unsigned     default 0                   not null comment 'Level of Fisher job',
    Goldsmith     tinyint unsigned     default 0                   not null comment 'Level of Goldsmith job',
    Gunbreaker    tinyint unsigned     default 0                   not null comment 'Level of Gunbreaker job',
    Leatherworker tinyint unsigned     default 0                   not null comment 'Level of Leatherworker job',
    Machinist     tinyint unsigned     default 0                   not null comment 'Level of Machinist job',
    Miner         tinyint unsigned     default 0                   not null comment 'Level of Miner job',
    Monk          tinyint unsigned     default 0                   not null comment 'Level of Monk job',
    Ninja         tinyint unsigned     default 0                   not null comment 'Level of Ninja job',
    Paladin       tinyint unsigned     default 0                   not null comment 'Level of Paladin job',
    Reaper        tinyint unsigned     default 0                   not null comment 'Level of Reaper job',
    RedMage       tinyint unsigned     default 0                   not null comment 'Level of Red Mage job',
    Sage          tinyint unsigned     default 0                   not null comment 'Level of Sage job',
    Samurai       tinyint unsigned     default 0                   not null comment 'Level of Samurai job',
    Scholar       tinyint unsigned     default 0                   not null comment 'Level of Scholar job',
    Summoner      tinyint unsigned     default 0                   not null comment 'Level of Summoner job',
    Warrior       tinyint unsigned     default 0                   not null comment 'Level of Warrior job',
    Weaver        tinyint unsigned     default 0                   not null comment 'Level of Weaver job',
    WhiteMage     tinyint unsigned     default 0                   not null comment 'Level of White Mage job',
    constraint cityid
        foreign key (cityid) references ffxiv__city (cityid)
            on update cascade,
    constraint clanid
        foreign key (clanid) references ffxiv__clan (clanid)
            on update cascade on delete set null,
    constraint enemyid
        foreign key (enemyid) references ffxiv__enemy (enemyid)
            on update cascade on delete set null,
    constraint gcrankid
        foreign key (gcrankid) references ffxiv__grandcompany_rank (gcrankid)
            on update cascade on delete set null,
    constraint guardianid
        foreign key (guardianid) references ffxiv__guardian (guardianid)
            on update cascade,
    constraint namedayid
        foreign key (namedayid) references ffxiv__nameday (namedayid)
            on update cascade,
    constraint serverid
        foreign key (serverid) references ffxiv__server (serverid)
            on update cascade on delete set null,
    constraint titleid
        foreign key (titleid) references ffxiv__achievement (achievementid)
            on update cascade on delete set null,
    constraint userid
        foreign key (userid) references uc__users (userid)
            on update cascade on delete set null
)
    comment 'Characters found on Lodestone';

create fulltext index biography
    on ffxiv__character (biography);

create index deleted
    on ffxiv__character (deleted);

create fulltext index name
    on ffxiv__character (name);

create index name_order
    on ffxiv__character (name);

create index registered
    on ffxiv__character (registered);

create index updated
    on ffxiv__character (updated desc);

