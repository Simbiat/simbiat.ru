create table ffxiv__nameday
(
    namedayid smallint(3) unsigned auto_increment comment 'Nameday ID as registered by the tracker'
        primary key,
    nameday   varchar(32) not null comment 'Nameday',
    constraint nameday
        unique (nameday)
)
    comment 'Namedays as per lore';

