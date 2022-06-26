create table talks__posts
(
    postid    int unsigned auto_increment comment 'Post ID',
    threadid  int unsigned                                    not null comment 'ID of a thread the post belongs to',
    replyto   int unsigned                                    null comment 'Indicates an ID of a post, that this is a reply to (required to build chains)',
    `system`  tinyint(1) unsigned default 0                   not null comment 'Flag indicating that post is system one, thus should not be deleted.',
    locked    tinyint(1) unsigned default 0                   not null comment 'Flag to indicate if post is locked from editing',
    created   timestamp           default current_timestamp() not null comment 'When post was created',
    createdby int unsigned                                    null comment 'User ID of the creator',
    updated   timestamp           default current_timestamp() not null comment 'When post was updated',
    updatedby int unsigned                                    null comment 'User ID of the last updater',
    text      longtext                                        not null comment 'Text of the post',
    constraint postid
        unique (postid),
    constraint post_created_by
        foreign key (createdby) references uc__users (userid)
            on update cascade on delete set null,
    constraint post_to_post
        foreign key (replyto) references talks__posts (postid)
            on update cascade on delete set null,
    constraint post_to_thread
        foreign key (threadid) references talks__threads (threadid)
            on update cascade,
    constraint post_updated_by
        foreign key (updatedby) references uc__users (userid)
            on update cascade on delete set null
)
    comment 'List of all posts';

create index created
    on talks__posts (created desc);

create fulltext index text
    on talks__posts (text);

