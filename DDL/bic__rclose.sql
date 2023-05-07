CREATE TABLE `bic__rclose`
(
    `R_CLOSE`   VARCHAR(2)  NOT NULL COMMENT 'Код причины закрытия номера счета' PRIMARY KEY,
    `NAMECLOSE` VARCHAR(45) NULL COMMENT 'Наименование причины закрытия'
) COMMENT 'Причина закрытия' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
