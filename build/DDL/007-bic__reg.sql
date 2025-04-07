USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `bic__reg` (
  `RGN` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Код территории Российской Федерации',
  `NAME` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Наименование территории в именительном падеже',
  `CENTER` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Наименование административного центра',
  PRIMARY KEY (`RGN`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Наименование территории' `PAGE_COMPRESSED`='ON';