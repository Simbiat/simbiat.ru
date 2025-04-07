USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `bic__rclose` (
  `R_CLOSE` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Код причины закрытия номера счета',
  `NAMECLOSE` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Наименование причины закрытия',
  PRIMARY KEY (`R_CLOSE`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Причина закрытия' `PAGE_COMPRESSED`='ON';