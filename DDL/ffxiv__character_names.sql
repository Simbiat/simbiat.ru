create table ffxiv__character_names
(
    characterid int unsigned not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    name        varchar(50)  not null comment 'Character''s previous name',
    primary key (characterid, name),
    constraint char_names_id
        foreign key (characterid) references ffxiv__character (characterid)
            on update cascade on delete cascade
)
    comment 'Past names used by characters';

