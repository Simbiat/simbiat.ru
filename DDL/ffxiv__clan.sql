create table ffxiv__clan
(
    clanid tinyint(2) unsigned not null comment 'Clan ID based on filters taken from Lodestone'
        primary key,
    clan   varchar(25)         not null comment 'Clan name',
    race   varchar(15)         not null comment 'Race name',
    raceid tinyint(2) unsigned not null comment 'Race ID based on filters taken from Lodestone',
    constraint clan
        unique (clan, race)
)
    comment 'Clans/races as per lore';

