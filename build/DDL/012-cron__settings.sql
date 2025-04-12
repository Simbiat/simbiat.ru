USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `cron__settings` (
  `setting` varchar(10) NOT NULL COMMENT 'Name of the setting',
  `value` varchar(10) DEFAULT NULL COMMENT 'Value of the setting',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Description of the setting',
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';