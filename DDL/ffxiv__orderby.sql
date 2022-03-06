create table ffxiv__orderby
(
    orderID     tinyint(1) unsigned not null comment 'ID based on filters from Lodestone'
        primary key,
    Description varchar(100)        not null comment 'Description of the order'
)
    comment 'ORDER BY options for searches on Lodestone';

