create table uc__user_to_email
(
    userid     int unsigned not null comment 'User ID',
    email      varchar(100) not null,
    activation text         null,
    primary key (userid, email),
    constraint email
        unique (email),
    constraint email_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade
);

