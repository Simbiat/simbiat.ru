create table uc__emails
(
    email      varchar(100)                  not null comment 'Email address'
        primary key,
    userid     int unsigned                  not null comment 'User ID',
    subscribed tinyint(1) unsigned default 0 not null comment 'Flag indicating, that this mail should receive notifications',
    activation text                          null comment 'Encrypted activation code',
    constraint email_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

create index subscribed
    on uc__emails (subscribed desc);

create index userid
    on uc__emails (userid);
