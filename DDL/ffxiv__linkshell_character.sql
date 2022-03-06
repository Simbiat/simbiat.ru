create table ffxiv__linkshell_character
(
    linkshellid varchar(40)                   not null comment 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)',
    characterid int unsigned                  not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    rankid      tinyint(1) unsigned default 3 not null comment 'Rank ID as registered by tracker',
    current     tinyint(1) unsigned default 0 not null comment 'Whether character is currently in the group',
    primary key (linkshellid, characterid),
    constraint link_char_char
        foreign key (characterid) references ffxiv__character (characterid)
            on update cascade on delete cascade,
    constraint link_char_link
        foreign key (linkshellid) references ffxiv__linkshell (linkshellid)
            on update cascade on delete cascade,
    constraint ls_rank2
        foreign key (rankid) references ffxiv__linkshell_rank (lsrankid)
            on update cascade on delete cascade
)
    comment 'Characters linked to linkshells, past and present';

create index `character`
    on ffxiv__linkshell_character (characterid);

create index ls_rank
    on ffxiv__linkshell_character (rankid);

