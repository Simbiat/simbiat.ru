create table talks__thread_to_tags
(
    threadid int unsigned not null comment 'Thread ID',
    tagid    int unsigned not null comment 'Tag ID',
    constraint threadid
        unique (threadid, tagid),
    constraint thr_tag_tag
        foreign key (tagid) references talks__tags (tagid)
            on update cascade on delete cascade,
    constraint thr_tag_thread
        foreign key (threadid) references talks__threads (threadid)
            on update cascade on delete cascade
)
    comment 'Threads to tags junction table';

