create table bic__acc_type
(
    RegulationAccountType varchar(4)   not null comment 'Тип счета в соответствии с нормативом'
        primary key,
    Description           varchar(100) not null comment 'Описание типа'
)
    comment 'Список типов счетов';

