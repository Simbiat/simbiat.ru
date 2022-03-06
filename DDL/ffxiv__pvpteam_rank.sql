create table ffxiv__pvpteam_rank
(
    pvprankid tinyint(1) unsigned auto_increment comment 'Rank ID as registered by tracker'
        primary key,
    `rank`    varchar(9)  not null comment 'Rank name',
    icon      varchar(20) null comment 'Name of the rank icon file'
)
    comment 'Rank names used by PvP teams';

create index pvprank
    on ffxiv__pvpteam_rank (`rank`);

