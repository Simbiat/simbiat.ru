create table uc__cookies
(
    cookieid  varchar(256)                          not null comment 'Cookie ID'
        primary key,
    userid    int unsigned                          not null comment 'ID of the user to which this cookie belongs',
    validator text                                  not null comment 'Encrypted validator string, to compare against cookie',
    time      timestamp default current_timestamp() not null on update current_timestamp() comment 'Time of last update/use of the cookie',
    ip        varchar(45)                           null comment 'Last IP, that used the cookie',
    useragent text                                  null comment 'Last UserAgent of the client, from which the cookie was used',
    constraint cookie_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

create index time
    on uc__cookies (time);
