create table uc__users
(
    userid     int unsigned auto_increment comment 'User ID'
        primary key,
    username   varchar(64)                                     not null comment 'User''s username/login',
    email      varchar(320)                                    not null comment 'User''s main mail',
    phone      bigint(15) unsigned                             null comment 'User''s phone number in international format',
    password   text                                            not null comment 'Hashed password',
    strikes    tinyint(2) unsigned default 0                   not null comment 'Number of unsuccessful logins',
    pw_reset   text                                            null comment 'Password reset code',
    registered timestamp           default current_timestamp() not null comment 'When user was registered',
    updated    timestamp           default current_timestamp() not null on update current_timestamp() comment 'When user was updated',
    parentid   int unsigned                                    null comment 'User ID, that added this one (if added manually)',
    birthday   date                                            null comment 'User''s date of birth',
    firstname  varchar(100)                                    null comment 'User''s first name',
    lastname   varchar(100)                                    null comment 'User''s last/family name (also known as surname)',
    middlename varchar(100)                                    null comment 'User''s middle name(s)',
    fathername varchar(100)                                    null comment 'User''s patronymic or matronymic name (also known as father''s name or mother''s name)',
    sex        tinyint(1) unsigned                             null comment 'User''s sex',
    avatar     varchar(1000)                                   null comment 'URI to current avatar',
    about      varchar(250)                                    null comment 'Introductory words from the user',
    timezone   varchar(30)         default 'UTC'               not null comment 'User''s timezone',
    constraint parent_to_user
        foreign key (parentid) references uc__users (userid)
            on update set null on delete set null
);

create index birthday
    on uc__users (birthday);

create index parentid
    on uc__users (parentid);

create index registered
    on uc__users (registered);

create index usergender
    on uc__users (sex);

