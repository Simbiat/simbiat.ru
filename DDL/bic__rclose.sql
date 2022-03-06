create table bic__rclose
(
    R_CLOSE   varchar(2)  not null comment 'Код причины закрытия номера счета'
        primary key,
    NAMECLOSE varchar(45) null comment 'Наименование причины закрытия'
)
    comment 'Причина закрытия';

