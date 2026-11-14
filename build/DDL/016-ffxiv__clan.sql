USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__clan` (
  `clan_id` tinyint(2) unsigned NOT NULL COMMENT 'Clan ID based on filters taken from Lodestone',
  `clan` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Clan name',
  `race` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Race name',
  `race_id` tinyint(2) unsigned NOT NULL COMMENT 'Race ID based on filters taken from Lodestone',
  PRIMARY KEY (`clan_id`),
  UNIQUE KEY `clan` (`clan`,`race`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Clans/races as per lore' `PAGE_COMPRESSED`='ON';