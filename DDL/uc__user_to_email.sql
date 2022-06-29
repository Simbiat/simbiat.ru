create table uc__user_to_email
(
    userid     int unsigned                  not null comment 'User ID',
    email      varchar(100)                  not null comment 'Email address',
    subscribed tinyint(1) unsigned default 0 not null comment 'Flag indicating, that this mail should receive notifications',
    activation text                          null comment 'Encrypted activation code',
    primary key (userid, email),
    constraint email
        unique (email),
    constraint email_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

create index subscribed
    on uc__user_to_email (subscribed desc);

