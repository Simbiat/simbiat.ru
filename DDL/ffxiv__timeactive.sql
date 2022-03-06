create table ffxiv__timeactive
(
    activeid tinyint(1) unsigned not null comment 'Active time ID based on filters from Lodestone'
        primary key,
    active   varchar(8)          not null comment 'Active time as shown on Lodestone',
    constraint active
        unique (active)
)
    comment 'IDs to identify when a free company is active';

