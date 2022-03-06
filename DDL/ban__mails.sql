create table ban__mails
(
    mail   varchar(320)                     not null comment 'Banned e-mail'
        primary key,
    added  date default current_timestamp() not null comment 'When e-mail was banned',
    reason text                             null comment 'Reason for the ban'
)
    comment 'Banned e-mail addresses';

create index added
    on ban__mails (added);

