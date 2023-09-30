CREATE TABLE `bic__rclose`
(
    `R_CLOSE`   VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci  NOT NULL COMMENT 'Код причины закрытия номера счета' PRIMARY KEY,
    `NAMECLOSE` VARCHAR(45) COLLATE utf8mb4_uca1400_nopad_ai_ci NULL COMMENT 'Наименование причины закрытия'
) COMMENT 'Причина закрытия' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
