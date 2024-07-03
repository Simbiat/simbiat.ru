CREATE TABLE `uc__groups` (
  `groupid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of the group',
  `groupname` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Human-ready group name',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Description of the group',
  PRIMARY KEY (`groupid`),
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';