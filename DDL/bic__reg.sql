CREATE TABLE `bic__reg`
(
    `RGN`    VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci  NOT NULL COMMENT 'Код территории Российской Федерации' PRIMARY KEY,
    `NAME`   VARCHAR(40) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Наименование территории в именительном падеже',
    `CENTER` VARCHAR(30) COLLATE utf8mb4_uca1400_nopad_ai_ci NULL COMMENT 'Наименование административного центра'
) COMMENT 'Наименование территории' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
