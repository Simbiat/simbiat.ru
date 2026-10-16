USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__linkshell_character` (
  `ls_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)',
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `rank_id` tinyint(1) unsigned NOT NULL DEFAULT 3 COMMENT 'Rank ID as registered by tracker',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Whether character is currently in the group',
  PRIMARY KEY (`ls_id`,`character_id`),
  KEY `character` (`character_id`),
  KEY `ls_rank` (`rank_id`),
  CONSTRAINT `link_char_char` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `link_char_link` FOREIGN KEY (`ls_id`) REFERENCES `ffxiv__linkshell` (`ls_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ls_rank2` FOREIGN KEY (`rank_id`) REFERENCES `ffxiv__linkshell_rank` (`ls_rank_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Characters linked to linkshells, past and present' `PAGE_COMPRESSED`='ON';