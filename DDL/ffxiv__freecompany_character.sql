create table ffxiv__freecompany_character
(
    characterid   int unsigned                  not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    freecompanyid varchar(20)                   not null comment 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
    rankid        tinyint(2) unsigned           null comment 'ID calculated based on rank icon on Lodestone',
    current       tinyint(1) unsigned default 0 not null comment 'Whether character is currently in the group',
    primary key (characterid, freecompanyid),
    constraint fc_char_rank
        foreign key (rankid) references ffxiv__freecompany_rank (rankid)
            on update set null on delete set null,
    constraint fc_xchar_fc
        foreign key (freecompanyid) references ffxiv__freecompany (freecompanyid)
            on update cascade on delete cascade,
    constraint fc_xchar_id
        foreign key (characterid) references ffxiv__character (characterid)
            on update cascade on delete cascade
)
    comment 'Characters linked to Free Companies, past and present';

