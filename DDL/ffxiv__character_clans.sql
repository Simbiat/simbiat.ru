create table ffxiv__character_clans
(
    characterid int unsigned        not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    genderid    tinyint(1) unsigned not null comment '0 for female and 1 for male',
    clanid      tinyint(2) unsigned not null comment 'Clan ID identifying both clan and race of the character',
    primary key (characterid, genderid, clanid),
    constraint char_clan_clan
        foreign key (clanid) references ffxiv__clan (clanid)
            on update cascade on delete cascade,
    constraint char_clan_id
        foreign key (characterid) references ffxiv__character (characterid)
            on update cascade on delete cascade
)
    comment 'Past clans used by characters';

