create table uc__users
(
    userid     int unsigned auto_increment comment 'User ID'
        primary key,
    username   varchar(64)                                     not null comment 'User''s username/login',
    phone      bigint(15) unsigned                             null comment 'User''s phone number in international format',
    password   text                                            not null comment 'Hashed password',
    strikes    tinyint(2) unsigned default 0                   not null comment 'Number of unsuccessful logins',
    pw_reset   text                                            null comment 'Password reset code',
    api_key    text                                            null comment 'API key',
    ff_token   text                                            not null comment 'Token for linking FFXIV characters',
    registered timestamp           default current_timestamp() not null comment 'When user was registered',
    updated    timestamp           default current_timestamp() not null on update current_timestamp() comment 'When user was updated',
    parentid   int unsigned                                    null comment 'User ID, that added this one (if added manually)',
    birthday   date                                            null comment 'User''s date of birth',
    firstname  varchar(100)                                    null comment 'User''s first name',
    lastname   varchar(100)                                    null comment 'User''s last/family name (also known as surname)',
    middlename varchar(100)                                    null comment 'User''s middle name(s)',
    fathername varchar(100)                                    null comment 'User''s patronymic or matronymic name (also known as father''s name or mother''s name)',
    prefix     varchar(25)                                     null comment 'The prefix or title, such as "Mrs.", "Mr.", "Miss", "Ms.", "Dr.", or "Mlle."',
    suffix     varchar(25)                                     null comment 'The suffix, such as "Jr.", "B.Sc.", "PhD.", "MBASW", or "IV"',
    sex        tinyint(1) unsigned                             null comment 'User''s sex',
    about      varchar(250)                                    null comment 'Introductory words from the user',
    timezone   varchar(30)         default 'UTC'               not null comment 'User''s timezone',
    country    varchar(60)                                     null comment 'User''s country',
    city       varchar(200)                                    null comment 'User''s city',
    website    varchar(255)                                    null comment 'User''s personal website',
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

