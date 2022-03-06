create table talks__posts_bck
(
    threadid    int(11) unsigned auto_increment
        primary key,
    date        timestamp default current_timestamp() not null,
    title       text                                  not null,
    type        text                                  not null,
    language    varchar(3)                            null,
    text        longtext                              not null,
    original    longtext                              null,
    description longtext                              null,
    copyright   text                                  null,
    song        text                                  null
);

create index date
    on talks__posts_bck (date);

