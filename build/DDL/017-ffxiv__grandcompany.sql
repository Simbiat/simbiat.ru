USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__grandcompany` (
  `gc_id` tinyint(1) unsigned NOT NULL COMMENT 'ID based on filters from Lodestone',
  `gc_name` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Name of the company',
  PRIMARY KEY (`gc_id`),
  UNIQUE KEY `gcName` (`gc_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Grand Companies as per lore' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;