USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__pvpteam_character` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `pvp_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)',
  `rank_id` tinyint(1) unsigned NOT NULL DEFAULT 3 COMMENT 'PvP team rank ID as registered by tracker',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Whether character is currently in the group',
  PRIMARY KEY (`character_id`,`pvp_id`),
  KEY `pvp_xchar_pvp` (`pvp_id`),
  KEY `pvp_char_rank` (`rank_id`),
  CONSTRAINT `pvp_char_rank` FOREIGN KEY (`rank_id`) REFERENCES `ffxiv__pvpteam_rank` (`pvp_rank_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pvp_xchar_id` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pvp_xchar_pvp` FOREIGN KEY (`pvp_id`) REFERENCES `ffxiv__pvpteam` (`pvp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Characters linked to PvP teams, past and present' `PAGE_COMPRESSED`='ON';