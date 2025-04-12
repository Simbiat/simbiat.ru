USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__timeactive` (
  `activeid` tinyint(1) unsigned NOT NULL COMMENT 'Active time ID based on filters from Lodestone',
  `active` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Active time as shown on Lodestone',
  PRIMARY KEY (`activeid`),
  UNIQUE KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='IDs to identify when a free company is active' `PAGE_COMPRESSED`='ON';