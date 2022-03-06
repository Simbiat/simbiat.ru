create table ffxiv__freecompany_rank
(
    freecompanyid varchar(20)         not null comment 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
    rankid        tinyint(2) unsigned not null comment 'ID calculated based on rank icon on Lodestone',
    rankname      varchar(15)         not null comment 'Name of the rank as reported by Lodestone',
    primary key (freecompanyid, rankid),
    constraint fcranks_freecompany
        foreign key (freecompanyid) references ffxiv__freecompany (freecompanyid)
            on update cascade on delete cascade
)
    comment 'Rank names used by companies';

create index rankid
    on ffxiv__freecompany_rank (rankid);

