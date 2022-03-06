create table ffxiv__count_filter
(
    countId tinyint(1) unsigned not null comment 'ID of filter by members count for groups'' search on Lodestone'
        primary key,
    value   varchar(5)          not null comment 'Value that is used Lodestone when filtering',
    constraint value
        unique (value)
)
    comment 'Filters by members count for groups'' search on Lodestone';

