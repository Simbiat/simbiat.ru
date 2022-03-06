create table ffxiv__character_servers
(
    characterid int unsigned        not null comment 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    serverid    tinyint(2) unsigned not null comment 'ID of the server character previously resided on',
    primary key (characterid, serverid),
    constraint char_serv_id
        foreign key (characterid) references ffxiv__character (characterid)
            on update cascade on delete cascade,
    constraint char_serv_serv
        foreign key (serverid) references ffxiv__server (serverid)
            on update cascade on delete cascade
)
    comment 'Past servers used by characters';

