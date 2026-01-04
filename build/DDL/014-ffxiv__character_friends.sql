USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_friends` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `friend` int(10) unsigned NOT NULL COMMENT 'Character ID of the friend taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Whether currently friends',
  PRIMARY KEY (`character_id`,`friend`),
  KEY `friend` (`friend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='List of character friends' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;