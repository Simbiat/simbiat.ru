create table uc__cookies
(
    cookieid  varchar(256)                          not null comment 'Cookie ID'
        primary key,
    validator text                                  not null comment 'Encrypted validator string, to compare against cookie',
    userid    int unsigned                          not null comment 'User ID',
    time      timestamp default current_timestamp() not null on update current_timestamp() comment 'Time of last update/use of the cookie',
    constraint cookie_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

create index time
    on uc__cookies (time);

create index usercookie
    on uc__cookies (userid);

