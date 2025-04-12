USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__tags` (
  `tagid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Tag ID',
  `tag` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Tag',
  PRIMARY KEY (`tagid`) USING BTREE,
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='List of tags' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;