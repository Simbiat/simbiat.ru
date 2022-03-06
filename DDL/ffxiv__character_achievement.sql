create table ffxiv__character_achievement
(
    characterid   int unsigned      not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    achievementid smallint unsigned not null comment 'Achievement ID taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/character/characterid/achievement/detail/achievementid/)',
    time          date              not null comment 'Date when achievement was received according to Lodestone',
    primary key (characterid, achievementid),
    constraint char_ach_ach
        foreign key (achievementid) references ffxiv__achievement (achievementid)
            on update cascade on delete cascade,
    constraint char_ach_char
        foreign key (characterid) references ffxiv__character (characterid)
            on update cascade on delete cascade
)
    comment 'Achievements linked to known characters';

create index ach
    on ffxiv__character_achievement (achievementid);

create index time
    on ffxiv__character_achievement (time);

