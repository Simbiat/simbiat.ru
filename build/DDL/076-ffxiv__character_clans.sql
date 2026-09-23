USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_clans` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `gender` tinyint(1) unsigned NOT NULL COMMENT '0 for female and 1 for male',
  `clan_id` tinyint(2) unsigned NOT NULL COMMENT 'Clan ID identifying both clan and race of the character',
  PRIMARY KEY (`character_id`,`gender`,`clan_id`),
  KEY `char_clan_clan` (`clan_id`),
  CONSTRAINT `char_clan_clan` FOREIGN KEY (`clan_id`) REFERENCES `ffxiv__clan` (`clan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `char_clan_id` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past clans used by characters' `PAGE_COMPRESSED`='ON';