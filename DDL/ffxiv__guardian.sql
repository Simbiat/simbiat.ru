create table ffxiv__guardian
(
    guardianid tinyint(2) unsigned auto_increment comment 'Guardian ID as registered by the tracker'
        primary key,
    guardian   varchar(25) not null comment 'Guardian name',
    constraint guardian
        unique (guardian)
)
    comment 'Guardians as per lore';

