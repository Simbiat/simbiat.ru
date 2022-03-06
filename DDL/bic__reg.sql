create table bic__reg
(
    RGN    varchar(2)  not null comment 'Код территории Российской Федерации'
        primary key,
    NAME   varchar(40) not null comment 'Наименование территории в именительном падеже',
    CENTER varchar(30) null comment 'Наименование административного центра'
)
    comment 'Наименование территории';

