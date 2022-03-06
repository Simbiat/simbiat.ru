create table ffxiv__linkshell_names
(
    linkshellid varchar(40) not null comment 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)',
    name        varchar(50) not null comment 'Previous Linkshell name',
    primary key (linkshellid, name),
    constraint ls_names_id
        foreign key (linkshellid) references ffxiv__linkshell (linkshellid)
            on update cascade on delete cascade
)
    comment 'Past names of linkshells';

