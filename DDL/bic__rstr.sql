CREATE TABLE `bic__rstr`
(
    `Rstr`        VARCHAR(4) COLLATE utf8mb4_uca1400_nopad_as_ci   NOT NULL COMMENT 'Код ограничения' PRIMARY KEY,
    `Description` VARCHAR(150) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Описание ограничения'
) COMMENT 'Код ограничений для участников и их счетов' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
