USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__estate` (
  `estate_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Estate ID as registered by the tracker',
  `city_id` tinyint(2) unsigned NOT NULL DEFAULT 5 COMMENT 'City ID as registered by the tracker',
  `area` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Estate area name',
  `ward` tinyint(3) unsigned NOT NULL COMMENT 'Ward number',
  `plot` tinyint(3) unsigned NOT NULL COMMENT 'Plot number',
  `size` tinyint(1) unsigned NOT NULL COMMENT 'Size of the house, where 1 is for small, 2 is for medium and 3 is for large',
  PRIMARY KEY (`estate_id`),
  UNIQUE KEY `address` (`area`,`ward`,`plot`) USING BTREE,
  KEY `estate_cityid` (`city_id`),
  CONSTRAINT `estate_cityid` FOREIGN KEY (`city_id`) REFERENCES `ffxiv__city` (`city_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='List of estates' `PAGE_COMPRESSED`='ON';