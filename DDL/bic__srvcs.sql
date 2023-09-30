CREATE TABLE `bic__srvcs`
(
    `Srvcs`       VARCHAR(1) COLLATE utf8mb4_uca1400_nopad_ai_ci   NOT NULL COMMENT 'Код сервиса' PRIMARY KEY,
    `Description` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Описание сервиса'
) COMMENT 'Коды сервисов доступных участникам обмена' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
