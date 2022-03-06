create table ban__names
(
    name   varchar(64)                      not null comment 'Banned (prohibited) name'
        primary key,
    added  date default current_timestamp() not null comment 'When name was banned',
    reason text                             null comment 'Reason for the ban'
)
    comment 'Banned user names';

create index added
    on ban__names (added);

