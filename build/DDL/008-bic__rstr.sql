CREATE TABLE IF NOT EXISTS `bic__rstr` (
  `Rstr` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Код ограничения',
  `Description` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Описание ограничения',
  PRIMARY KEY (`Rstr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Код ограничений для участников и их счетов' `PAGE_COMPRESSED`='ON';