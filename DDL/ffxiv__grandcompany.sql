create table ffxiv__grandcompany
(
    gcId   tinyint(1) unsigned not null comment 'ID based on filters from Lodestone'
        primary key,
    gcName varchar(25)         not null comment 'Name of the company',
    constraint gcName
        unique (gcName)
)
    comment 'Grand Companies as per lore';

