CREATE TABLE IF NOT EXISTS `ffxiv__jobs` (
  `jobid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Job ID',
  `name` varchar(20) NOT NULL COMMENT 'Job name',
  PRIMARY KEY (`jobid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_as_cs COMMENT='Jobs available in FFXIV' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;