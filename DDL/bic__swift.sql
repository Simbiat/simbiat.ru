create table bic__swift
(
    BIC          int(9) unsigned zerofill                        not null comment 'БИК код',
    SWBIC        varchar(11)                                     not null comment 'Банковский идентификационный код, присвоенный SWIFT',
    DefaultSWBIC tinyint(1) unsigned default 0                   not null comment 'Признак использования БИК (СВИФТ), «по умолчанию»',
    DateIn       date                default current_timestamp() null comment 'Дата добавления кода',
    DateOut      date                                            null comment 'Дата удаления кода',
    primary key (BIC, SWBIC),
    constraint bic_swift
        foreign key (BIC) references bic__list (BIC)
            on update cascade on delete cascade
)
    comment 'Коды SWIFT';

create index DateOut
    on bic__swift (DateOut desc);

create index DefaultSWBIC
    on bic__swift (DefaultSWBIC desc);

create index SWBIC
    on bic__swift (SWBIC);

