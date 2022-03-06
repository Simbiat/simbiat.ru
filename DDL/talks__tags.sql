create table talks__tags
(
    tagid int unsigned auto_increment comment 'Tag ID',
    tag   varchar(25) not null comment 'Tag',
    constraint tag
        unique (tag),
    constraint tagid
        unique (tagid)
)
    comment 'List of tags';

