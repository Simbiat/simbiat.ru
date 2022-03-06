create table bic__list
(
    BIC        int(9) unsigned zerofill               not null comment 'Банковский идентификационный код (БИК)'
        primary key,
    DateIn     date       default current_timestamp() null comment 'Дата включения в состав участников перевода',
    DateOut    date                                   null comment 'Дата исключения информации об Участнике',
    Updated    timestamp  default current_timestamp() null comment 'Дата изменения записи',
    NameP      varchar(160)                           not null comment 'Наименование участника расчетов',
    EnglName   varchar(140)                           null comment 'Наименование участника на английском языке',
    XchType    tinyint(1) default 0                   not null comment 'Участник обмена (0 - не участник)',
    PtType     varchar(2) default '99'                not null comment 'Код типа участника расчетов',
    Srvcs      varchar(1)                             null comment 'Доступные сервисы перевода денежных средств',
    UID        varchar(10)                            null comment 'Уникальный идентификатор составителя ЭС; УИС',
    PrntBIC    int(9) unsigned                        null comment 'БИК головной организации',
    CntrCd     varchar(2)                             null comment 'Код страны',
    RegN       varchar(9)                             null comment 'Регистрационный порядковый номер',
    Ind        varchar(6)                             null comment 'Индекс',
    Rgn        varchar(2) default '00'                not null comment 'Код территории',
    Tnp        varchar(5)                             null comment 'Тип населённого пункта',
    Nnp        varchar(25)                            null comment 'Наименование населённого пункта',
    Adr        varchar(160)                           null comment 'Адрес',
    DATE_CH    date                                   null comment 'Дата вступления в силу',
    NAMEN      varchar(30)                            null comment 'Наименование участника расчетов для поиска в ЭБД',
    NAMEMAXB   varchar(140)                           null comment 'Фирменное (полное официальное) наименование кредитной организации (KEYBASEB.DBF)',
    SWIFT_NAME varchar(90)                            null comment 'Имя банка в системе SWIFT из старого DBF (NAME_SRUS)',
    OLD_NEWNUM int(9) unsigned zerofill               null comment 'У некоторых старых записей БИК неуникален, что противоречит более новым схемам. Для сохранения информации, вводится эта колонка.',
    VKEY       varchar(8)                             null comment 'Уникальный внутренний код',
    VKEYDEL    varchar(8)                             null comment 'Уникальный внутренний код преемника',
    BVKEY      varchar(8)                             null comment 'Внутренний код участника расчётов по ЭБД «Книги ГРКО» (KEYBASEB.DBF)',
    FVKEY      varchar(8)                             null comment 'Внутренний код участника расчётов по ЭБД «Книги ГРКО» (KEYBASEF.dbf)',
    AT1        varchar(7)                             null comment 'Абонентский телеграф 1',
    AT2        varchar(7)                             null comment 'Абонентский телеграф 2',
    CKS        varchar(6)                             null comment 'Номер установки центра коммутации сообщений',
    TELEF      varchar(25)                            null comment 'Телефон',
    SROK       varchar(2)                             null comment 'Срок прохождения документов',
    NEWKS      varchar(9)                             null comment 'Корреспондентский счет (субсчет), действовавший до перехода на новый План счетов бухгалтерского учета',
    OKPO       varchar(8)                             null comment 'Код ОКПО',
    PERMFO     varchar(6)                             null comment 'Номер МФО',
    RKC        int(9) unsigned zerofill               null comment 'БИК РКЦ (ГРКЦ)',
    R_CLOSE    varchar(2)                             null comment 'Код причины закрытия номера счета',
    PRIM1      varchar(30)                            null comment 'Основание для ограничения участия в расчётах или исключения из состава участников расчётов (PRIM.dbf)',
    PRIM2      varchar(34)                            null comment 'Реквизиты ликвидационной комиссии (PRIM.dbf)',
    PRIM3      varchar(30)                            null comment 'Основание для аннулировании в «Книге ГРКО» записи о регистрации кредитной организации (филиала) (PRIM.dbf)',
    constraint bic__pzn
        foreign key (PtType) references bic__pzn (PtType)
            on update cascade,
    constraint bic__rclose
        foreign key (R_CLOSE) references bic__rclose (R_CLOSE)
            on update cascade on delete set null,
    constraint bic__rgn
        foreign key (Rgn) references bic__reg (RGN)
            on update cascade,
    constraint bic__service
        foreign key (Srvcs) references bic__srvcs (Srvcs)
            on update cascade on delete set null
)
    comment 'Список банков (BNKSEEK + BNKDEL)';

create fulltext index Adr
    on bic__list (Adr);

create index DATEDEL
    on bic__list (DateOut);

create index DT_IZM
    on bic__list (Updated);

create fulltext index NameP
    on bic__list (NameP);

create index OLD_NEWNUM
    on bic__list (OLD_NEWNUM);

create index REGN
    on bic__list (RegN);

create index RKC
    on bic__list (RKC);

create index VKEY
    on bic__list (VKEY);

create index VKEYDEL
    on bic__list (VKEYDEL);

