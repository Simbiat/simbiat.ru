USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_following` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `following` int(10) unsigned NOT NULL COMMENT 'Character ID being followed taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Whether currently following',
  PRIMARY KEY (`character_id`,`following`),
  KEY `following` (`following`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='List of character followers' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;