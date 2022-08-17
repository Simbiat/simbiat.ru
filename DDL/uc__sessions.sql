create table uc__sessions
(
    sessionid varchar(256)                                    not null comment 'Session''s UID'
        primary key,
    cookieid  varchar(256)                                    null comment 'Cookie associated with the session',
    time      timestamp           default current_timestamp() not null on update current_timestamp() comment 'Last time session was determined active',
    userid    int unsigned                                    null comment 'User ID, in case session is related to a user. Needed mainly for invalidation in case of complete user deletion.',
    bot       tinyint(1) unsigned default 0                   not null comment 'Whether session was determined to be a bot',
    ip        varchar(45)                                     null comment 'Session''s IP',
    useragent text                                            null comment 'UserAgent used in session',
    username  varchar(64)                                     null comment 'Name of either user (if logged in) or bot, if session belongs to one',
    page      varchar(256)                                    null comment 'Which page is being viewed at the moment',
    data      text                                            null comment 'Session''s data. Not meant for sensitive information.',
    constraint session_to_cookie
        foreign key (cookieid) references uc__cookies (cookieid)
            on update cascade on delete set null,
    constraint session_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

create index bot
    on uc__sessions (bot);

create index time
    on uc__sessions (time desc);

create index username
    on uc__sessions (username);

create index viewing
    on uc__sessions (page);
