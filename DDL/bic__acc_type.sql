CREATE TABLE `bic__acc_type`
(
    `RegulationAccountType` VARCHAR(4) COLLATE utf8mb4_uca1400_nopad_as_ci   NOT NULL COMMENT 'Тип счета в соответствии с нормативом' PRIMARY KEY,
    `Description`           VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Описание типа'
) COMMENT 'Список типов счетов' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
