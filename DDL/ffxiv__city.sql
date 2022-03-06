create table ffxiv__city
(
    cityid tinyint(1) unsigned auto_increment comment 'City ID as registered by the tracker'
        primary key,
    city   varchar(25) not null comment 'Name of the starting city',
    region varchar(25) not null comment 'Name of the region the city is located in',
    constraint city
        unique (city)
)
    comment 'Known cities';

