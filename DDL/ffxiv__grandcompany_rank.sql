create table ffxiv__grandcompany_rank
(
    gcrankid tinyint(2) unsigned auto_increment comment 'ID of character''s Grand Company''s affiliation and current rank there as registered by tracker'
        primary key,
    gcId     tinyint(1) unsigned not null comment 'Grand Company ID based on filters from Lodestone',
    gc_rank  varchar(50)         not null comment 'Rank name',
    constraint gc_rank
        unique (gc_rank),
    constraint gcRank_to_gc
        foreign key (gcId) references ffxiv__grandcompany (gcId)
            on update cascade on delete cascade
)
    comment 'Grand Companies'' ranks';

