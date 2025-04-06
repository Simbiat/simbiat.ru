CREATE TABLE IF NOT EXISTS `bic__pzn` (
  `PtType` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Код типа участника расчетов',
  `NAME` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Полное наименование типа участника расчетов',
  `active` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Флаг, говорящий является тип активным',
  PRIMARY KEY (`PtType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Тип организации' `PAGE_COMPRESSED`='ON';