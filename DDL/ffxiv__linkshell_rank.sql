create table ffxiv__linkshell_rank
(
    lsrankid tinyint(1) unsigned auto_increment comment 'Rank ID as registered by tracker'
        primary key,
    `rank`   varchar(6)  not null comment 'Rank name',
    icon     varchar(20) null comment 'Name of the rank icon file'
)
    comment 'Rank names used by linkshells';

create index lsrank
    on ffxiv__linkshell_rank (`rank`);

