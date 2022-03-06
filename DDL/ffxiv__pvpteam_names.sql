create table ffxiv__pvpteam_names
(
    pvpteamid varchar(40) not null comment 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)',
    name      varchar(50) not null comment 'Previous PvP Team name',
    primary key (pvpteamid, name),
    constraint pvp_name_id
        foreign key (pvpteamid) references ffxiv__pvpteam (pvpteamid)
            on update cascade on delete cascade
)
    comment 'Past names of PvP teams';

