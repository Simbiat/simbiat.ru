USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__city` (
  `cityid` tinyint(1) unsigned NOT NULL AUTO_INCREMENT COMMENT 'City ID as registered by the tracker',
  `city` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Name of the starting city',
  `region` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Name of the region the city is located in',
  PRIMARY KEY (`cityid`),
  UNIQUE KEY `city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Known cities' `PAGE_COMPRESSED`='ON';