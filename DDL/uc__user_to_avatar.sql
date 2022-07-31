create table uc__user_to_avatar
(
    userid      int unsigned                  not null comment 'User ID',
    characterid int unsigned                  not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    url         varchar(255)                  not null comment 'Link to file',
    current     tinyint(1) unsigned default 0 not null comment 'Flag to show if this avatar is the current one',
    primary key (userid, url),
    constraint avatar_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

create index avatarffcharid
    on uc__user_to_avatar (characterid);

