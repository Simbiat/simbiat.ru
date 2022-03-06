create table bic__bic_rstr
(
    BIC      int(9) unsigned zerofill         not null comment 'БИК участника',
    Rstr     varchar(4)                       not null comment 'Код ограничения, наложенного на участника',
    RstrDate date default current_timestamp() not null comment 'Дата начала действия ограничения участника',
    DateOut  date                             null comment 'Дата окончания действия ограничения участника',
    primary key (BIC, Rstr, RstrDate),
    constraint rstr_to_bic
        foreign key (BIC) references bic__list (BIC)
            on update cascade on delete cascade,
    constraint rstr_to_rstr
        foreign key (Rstr) references bic__rstr (Rstr)
            on update cascade on delete cascade
)
    comment 'Список ограничений наложенных на участника';

