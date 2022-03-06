create table bic__srvcs
(
    Srvcs       varchar(1)   not null comment 'Код сервиса'
        primary key,
    Description varchar(100) not null comment 'Описание сервиса'
)
    comment 'Коды сервисов доступных участникам обмена';

