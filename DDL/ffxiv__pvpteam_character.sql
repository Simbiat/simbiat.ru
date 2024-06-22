CREATE TABLE `ffxiv__pvpteam_character` (
  `characterid` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `pvpteamid` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)',
  `rankid` tinyint(1) unsigned NOT NULL DEFAULT 3 COMMENT 'PvP team rank ID as registered by tracker',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Whether character is currently in the group',
  PRIMARY KEY (`characterid`,`pvpteamid`),
  KEY `pvp_xchar_pvp` (`pvpteamid`),
  KEY `pvp_char_rank` (`rankid`),
  CONSTRAINT `pvp_char_rank` FOREIGN KEY (`rankid`) REFERENCES `ffxiv__pvpteam_rank` (`pvprankid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pvp_xchar_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pvp_xchar_pvp` FOREIGN KEY (`pvpteamid`) REFERENCES `ffxiv__pvpteam` (`pvpteamid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Characters linked to PvP teams, past and present' `PAGE_COMPRESSED`='ON';