create table bic__pzn
(
    PtType varchar(2)                    not null comment 'Код типа участника расчетов'
        primary key,
    NAME   varchar(160)                  null comment 'Полное наименование типа участника расчетов',
    active tinyint(1) unsigned default 0 not null comment 'Флаг, говорящий является тип активным'
)
    comment 'Тип организации';

