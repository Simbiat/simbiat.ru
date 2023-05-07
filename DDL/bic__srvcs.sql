CREATE TABLE `bic__srvcs`
(
    `Srvcs`       VARCHAR(1)   NOT NULL COMMENT 'Код сервиса' PRIMARY KEY,
    `Description` VARCHAR(100) NOT NULL COMMENT 'Описание сервиса'
) COMMENT 'Коды сервисов доступных участникам обмена' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
