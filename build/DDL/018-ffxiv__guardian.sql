USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__guardian` (
  `guardian_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Guardian ID as registered by the tracker',
  `guardian` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Guardian name',
  PRIMARY KEY (`guardian_id`),
  UNIQUE KEY `guardian` (`guardian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Guardians as per lore' `PAGE_COMPRESSED`='ON';