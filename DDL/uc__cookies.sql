create table uc__cookies
(
    cookieid  varchar(64)                           not null,
    userid    int unsigned                          not null comment 'User ID',
    time      timestamp default current_timestamp() not null,
    ip        varchar(45)                           not null,
    useragent text                                  null,
    constraint id
        unique (cookieid),
    constraint cookie_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

create index time
    on uc__cookies (time);

create index usercookie
    on uc__cookies (userid);

