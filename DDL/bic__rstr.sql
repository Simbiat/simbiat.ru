CREATE TABLE `bic__rstr`
(
    `Rstr`        VARCHAR(4)   NOT NULL COMMENT 'Код ограничения' PRIMARY KEY,
    `Description` VARCHAR(150) NOT NULL COMMENT 'Описание ограничения'
) COMMENT 'Код ограничений для участников и их счетов' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
