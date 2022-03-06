create table ffxiv__estate
(
    estateid smallint unsigned auto_increment comment 'Estate ID as registered by the tracker'
        primary key,
    cityid   tinyint(2) unsigned default 5 not null comment 'City ID as registered by the tracker',
    area     varchar(20)                   not null comment 'Estate area name',
    ward     tinyint unsigned              not null comment 'Ward number',
    plot     tinyint unsigned              not null comment 'Plot number',
    size     tinyint(1) unsigned           not null comment 'Size of the house, where 1 is for small, 2 is for medium and 3 is for large',
    constraint address
        unique (area, ward, plot),
    constraint estate_cityid
        foreign key (cityid) references ffxiv__city (cityid)
            on update cascade on delete cascade
)
    comment 'List of estates';

