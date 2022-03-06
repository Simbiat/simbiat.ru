create table ffxiv__freecompany_ranking
(
    freecompanyid varchar(20)                                      not null comment 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
    date          date                 default current_timestamp() not null comment 'Date of the ranking as identified by tracker',
    weekly        smallint(3) unsigned default 500                 not null comment 'Weekly ranking as reported by Lodestone',
    monthly       smallint(3) unsigned default 500                 not null comment 'Monthly ranking as reported by Lodestone',
    members       smallint(3) unsigned default 1                   not null comment 'Number of registered members at the date of rank update',
    primary key (freecompanyid, date),
    constraint fc_ranking_id
        foreign key (freecompanyid) references ffxiv__freecompany (freecompanyid)
            on update cascade on delete cascade
)
    comment 'Companies'' weekly and monthly rankings linked to members count';

