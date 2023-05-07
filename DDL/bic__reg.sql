CREATE TABLE `bic__reg`
(
    `RGN`    VARCHAR(2)  NOT NULL COMMENT 'Код территории Российской Федерации' PRIMARY KEY,
    `NAME`   VARCHAR(40) NOT NULL COMMENT 'Наименование территории в именительном падеже',
    `CENTER` VARCHAR(30) NULL COMMENT 'Наименование административного центра'
) COMMENT 'Наименование территории' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
