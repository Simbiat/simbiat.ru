USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ban__mails` (
  `mail` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Banned e-mail',
  `added` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When e-mail was banned',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Reason for the ban',
  PRIMARY KEY (`mail`),
  KEY `added` (`added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Banned e-mail addresses' `PAGE_COMPRESSED`='ON';