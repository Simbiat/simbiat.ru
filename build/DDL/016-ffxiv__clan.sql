CREATE TABLE IF NOT EXISTS `ffxiv__clan` (
  `clanid` tinyint(2) unsigned NOT NULL COMMENT 'Clan ID based on filters taken from Lodestone',
  `clan` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Clan name',
  `race` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Race name',
  `raceid` tinyint(2) unsigned NOT NULL COMMENT 'Race ID based on filters taken from Lodestone',
  PRIMARY KEY (`clanid`),
  UNIQUE KEY `clan` (`clan`,`race`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Clans/races as per lore' `PAGE_COMPRESSED`='ON';