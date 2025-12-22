USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_achievement` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `achievement_id` smallint(5) unsigned NOT NULL COMMENT 'Achievement ID taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/character/characterid/achievement/detail/achievementid/)',
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'Time when achievement was received according to Lodestone',
  PRIMARY KEY (`character_id`,`achievement_id`) USING BTREE,
  KEY `ach` (`achievement_id`) USING BTREE,
  KEY `time` (`time` DESC),
  CONSTRAINT `char_ach_ach` FOREIGN KEY (`achievement_id`) REFERENCES `ffxiv__achievement` (`achievement_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `char_ach_char` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Achievements linked to known characters' `PAGE_COMPRESSED`='ON';