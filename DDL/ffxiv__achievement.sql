create table ffxiv__achievement
(
    achievementid smallint unsigned                     not null comment 'Achievement ID taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/character/characterid/achievement/detail/achievementid/)'
        primary key,
    name          varchar(100)                          not null comment 'Name of achievement',
    registered    date      default current_timestamp() not null comment 'When achievement was initially added to tracker',
    updated       timestamp default current_timestamp() not null on update current_timestamp() comment 'When achievement was last updated on the tracker',
    category      varchar(30)                           null comment 'Category of the achievement',
    subcategory   varchar(30)                           null comment 'Subcategory of the achievement',
    icon          varchar(150)                          null comment 'Achievement icon without base URL (https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/)',
    howto         text                                  null comment 'Instructions on getting achievements taken from Lodestone',
    points        tinyint unsigned                      null comment 'Amount of points assigned to character for getting the achievement',
    title         varchar(50)                           null comment 'Optional title rewarded to character',
    item          varchar(100)                          null comment 'Optional item rewarded to character',
    itemicon      varchar(150)                          null comment 'Icon for optional item without base URL (https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/)',
    itemid        varchar(11)                           null comment 'ID of optional item taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/playguide/db/item/itemid/)',
    dbid          varchar(11)                           null comment 'ID of achievement in Lodestone database (https://eu.finalfantasyxiv.com/lodestone/playguide/db/achievement/dbid/)',
    constraint dbid
        unique (dbid),
    constraint itemid
        unique (itemid)
)
    comment 'Achievements found on Lodestone';

create fulltext index howto
    on ffxiv__achievement (howto);

create fulltext index name
    on ffxiv__achievement (name);

create index name_order
    on ffxiv__achievement (name);

create index updated
    on ffxiv__achievement (updated desc);

