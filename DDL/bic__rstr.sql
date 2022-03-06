create table bic__rstr
(
    Rstr        varchar(4)   not null comment 'Код ограничения'
        primary key,
    Description varchar(150) not null comment 'Описание ограничения'
)
    comment 'Код ограничений для участников и их счетов';

