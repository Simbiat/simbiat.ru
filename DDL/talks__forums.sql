create table talks__forums
(
    forumid   int unsigned auto_increment comment 'Forum ID',
    name      varchar(50)                                     not null comment 'Forum name',
    parentid  int unsigned                                    null comment 'ID of the parent forum',
    type      tinyint unsigned    default 1                   not null comment 'Type of the forum',
    `system`  tinyint(1) unsigned default 0                   null comment 'Flag indicating that forum is system one, thus should not be deleted.',
    closed    timestamp                                       null comment 'Flag indicating if the forum is closed',
    private   tinyint(1) unsigned default 1                   not null comment 'Flag indicating if forum is private',
    created   timestamp           default current_timestamp() not null comment 'When forum was created',
    createdby int unsigned                                    null comment 'User ID of the creator',
    updated   timestamp           default current_timestamp() not null comment 'When forum was updated',
    updatedby int unsigned                                    null comment 'User ID of the last updater',
    icon      varchar(50)                                     null comment 'Icon override',
    constraint forumid
        unique (forumid),
    constraint forum_created_by
        foreign key (createdby) references uc__users (userid)
            on update cascade on delete set null,
    constraint forum_to_forum
        foreign key (parentid) references talks__forums (forumid)
            on update cascade on delete set null,
    constraint forum_updated_by
        foreign key (updatedby) references uc__users (userid)
            on update cascade on delete set null
)
    comment 'List of forums';

create fulltext index name
    on talks__forums (name);

