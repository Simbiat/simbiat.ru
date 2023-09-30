CREATE TABLE `bic__pzn`
(
    `PtType` VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci   NOT NULL COMMENT 'Код типа участника расчетов' PRIMARY KEY,
    `NAME`   VARCHAR(160) COLLATE utf8mb4_uca1400_nopad_ai_ci NULL COMMENT 'Полное наименование типа участника расчетов',
    `active` TINYINT(1) UNSIGNED DEFAULT 0                    NOT NULL COMMENT 'Флаг, говорящий является тип активным'
) COMMENT 'Тип организации' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
