USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `bic__settings` (
  `setting` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Name of the setting',
  `value` varchar(100) NOT NULL COMMENT 'Value of the setting',
  `description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Optional description of the setting',
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='List of settings for BIC Tracker' `PAGE_COMPRESSED`='ON';