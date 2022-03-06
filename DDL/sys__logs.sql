create table sys__logs
(
    time      timestamp default current_timestamp() not null comment 'When action occurred',
    type      tinyint(2) unsigned                   not null comment 'Type of the action',
    action    varchar(255)                          not null comment 'Short description of the action',
    userid    int unsigned                          null comment 'Optional user ID, if available',
    ip        varchar(45)                           null comment 'IP, if available',
    useragent text                                  null comment 'Full useragent, if available',
    extra     text                                  null comment 'Any extra information available for the entry',
    constraint audit_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade,
    constraint log_type
        foreign key (type) references sys__log_types (typeid)
            on update cascade on delete cascade
)
    comment 'Table storing logs';

create index audit_userid
    on sys__logs (userid);

create index time
    on sys__logs (time);

