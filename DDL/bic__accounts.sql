create table bic__accounts
(
    Account               varchar(20)                      not null comment 'Номер счёта',
    BIC                   int(9) unsigned zerofill         not null comment 'БИК участника',
    AccountCBRBIC         int(9) unsigned zerofill         null comment 'БИК ПБР, обслуживающего счёт участника перевода',
    RegulationAccountType varchar(4)                       not null comment 'Тип счета в соответствии с нормативом',
    CK                    varchar(2)                       null comment 'Контрольный ключ',
    DateIn                date default current_timestamp() not null comment 'Дата открытия счета',
    DateOut               date                             null comment 'Дата исключения информации о счете участника',
    primary key (Account, BIC),
    constraint account_to_bic
        foreign key (BIC) references bic__list (BIC)
            on update cascade on delete cascade,
    constraint account_to_cbr
        foreign key (AccountCBRBIC) references bic__list (BIC)
            on update cascade on delete cascade,
    constraint account_to_type
        foreign key (RegulationAccountType) references bic__acc_type (RegulationAccountType)
            on update cascade on delete cascade
)
    comment 'Список счетов';

