create table talks__attachments
(
    fileid    int unsigned                                not null comment 'File ID',
    postid    int unsigned                                not null comment 'ID of the post to which the file is attached',
    name      varchar(128)                                not null comment 'Name of the file to be shown to humans',
    mime      varchar(100)                                not null comment 'MIME Type',
    size      bigint unsigned default 0                   not null comment 'Size of the file in bytes',
    hash      varchar(256)                                not null comment 'File hash. Also used as file name on file system.',
    added     timestamp       default current_timestamp() not null comment 'When file was added',
    downloads int unsigned    default 0                   not null comment 'Number of downloads',
    constraint fileid
        unique (fileid),
    constraint hash
        unique (hash),
    constraint file_to_post
        foreign key (postid) references talks__posts (postid)
            on update cascade
)
    comment 'List of file attachments';

create index mime
    on talks__attachments (mime);

