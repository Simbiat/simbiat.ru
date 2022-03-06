create table uc__user_to_avatar
(
    userid   int unsigned           not null comment 'User ID',
    ffcharid int unsigned default 0 not null,
    avatar   text                   not null,
    primary key (userid, ffcharid),
    constraint avatar_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

create index avatarffcharid
    on uc__user_to_avatar (ffcharid);

