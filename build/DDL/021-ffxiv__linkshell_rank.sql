CREATE TABLE IF NOT EXISTS `ffxiv__linkshell_rank` (
  `lsrankid` tinyint(1) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rank ID as registered by tracker',
  `rank` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Rank name',
  `icon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci DEFAULT NULL COMMENT 'Name of the rank icon file',
  PRIMARY KEY (`lsrankid`),
  KEY `lsrank` (`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Rank names used by linkshells' `PAGE_COMPRESSED`='ON';