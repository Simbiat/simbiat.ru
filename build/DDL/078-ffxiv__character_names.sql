USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_names` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `name` varchar(50) NOT NULL COMMENT 'Character''s previous name',
  PRIMARY KEY (`character_id`,`name`),
  FULLTEXT KEY `name` (`name`),
  CONSTRAINT `char_names_id` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past names used by characters' `PAGE_COMPRESSED`='ON';