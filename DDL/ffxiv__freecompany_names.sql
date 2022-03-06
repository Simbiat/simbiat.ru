create table ffxiv__freecompany_names
(
    freecompanyid varchar(20) not null comment 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
    name          varchar(50) not null comment 'Previous name of the company',
    primary key (freecompanyid, name),
    constraint fc_names_id
        foreign key (freecompanyid) references ffxiv__freecompany (freecompanyid)
            on update cascade on delete cascade
)
    comment 'Past names of the companies';

