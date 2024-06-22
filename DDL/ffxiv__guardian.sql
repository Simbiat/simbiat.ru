CREATE TABLE `ffxiv__guardian` (
  `guardianid` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Guardian ID as registered by the tracker',
  `guardian` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Guardian name',
  PRIMARY KEY (`guardianid`),
  UNIQUE KEY `guardian` (`guardian`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Guardians as per lore' `PAGE_COMPRESSED`='ON';