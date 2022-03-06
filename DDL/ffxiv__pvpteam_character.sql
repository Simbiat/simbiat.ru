create table ffxiv__pvpteam_character
(
    characterid int unsigned                  not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    pvpteamid   varchar(40)                   not null comment 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)',
    rankid      tinyint(1) unsigned default 3 not null comment 'PvP team rank ID as registered by tracker',
    current     tinyint(1) unsigned default 0 not null comment 'Whether character is currently in the group',
    primary key (characterid, pvpteamid),
    constraint pvp_char_rank
        foreign key (rankid) references ffxiv__pvpteam_rank (pvprankid)
            on update cascade on delete cascade,
    constraint pvp_xchar_id
        foreign key (characterid) references ffxiv__character (characterid)
            on update cascade on delete cascade,
    constraint pvp_xchar_pvp
        foreign key (pvpteamid) references ffxiv__pvpteam (pvpteamid)
            on update cascade on delete cascade
)
    comment 'Characters linked to PvP teams, past and present';

