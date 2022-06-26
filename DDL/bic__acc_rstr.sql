create table bic__acc_rstr
(
    Account      varchar(20)                      not null comment 'Номер счёта'
        primary key,
    AccRstr      varchar(4)                       not null comment 'Код ограничения операций по счёту',
    AccRstrDate  date default current_timestamp() not null comment 'Дата начала действия Ограничения операций по счёту',
    DateOut      date                             null comment 'Дата конца действия Ограничения операций по счёту',
    SuccessorBIC int(9) unsigned zerofill         null comment 'БИК преемника',
    constraint acc_to_acc
        foreign key (Account) references bic__accounts (Account)
            on update cascade on delete cascade,
    constraint acc_to_cbr
        foreign key (SuccessorBIC) references bic__list (BIC)
            on update cascade on delete cascade,
    constraint acc_to_rstr
        foreign key (AccRstr) references bic__rstr (Rstr)
            on update cascade on delete cascade
)
    comment 'Список ограничений наложенных на счета';

create index AccRstrDate
    on bic__acc_rstr (AccRstrDate desc);

