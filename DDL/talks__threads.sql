create table talks__threads
(
    threadid  int unsigned auto_increment comment 'Thread ID',
    name      varchar(100)                                    not null comment 'Thread name',
    forumid   int unsigned                                    not null comment 'Forum ID where the thread is located',
    language  varchar(35)         default 'en'                not null comment 'Main language of the thread',
    pinned    tinyint(1) unsigned default 0                   not null comment 'Flag to indicate if a thread needs to e shown above others in the list',
    closed    timestamp                                       null comment 'Flag to indicate if a thread is closed',
    private   tinyint(1) unsigned default 0                   not null comment 'Flag to indicate if thread is private',
    created   timestamp           default current_timestamp() not null comment 'When thread was created',
    createdby int unsigned                                    null comment 'User ID of the creator',
    updated   timestamp           default current_timestamp() not null comment 'When thread was updated',
    updatedby int unsigned                                    null comment 'User ID of the updater',
    constraint threadid
        unique (threadid),
    constraint thread_created_by
        foreign key (createdby) references uc__users (userid)
            on update cascade on delete set null,
    constraint thread_language
        foreign key (language) references sys__languages (tag)
            on update cascade,
    constraint thread_to_forum
        foreign key (forumid) references talks__forums (forumid)
            on update cascade,
    constraint thread_updated_by
        foreign key (updatedby) references uc__users (userid)
            on update cascade on delete set null
)
    comment 'List of threads';

create fulltext index name
    on talks__threads (name);

