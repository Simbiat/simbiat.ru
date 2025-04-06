CREATE TABLE IF NOT EXISTS `ffxiv__character_names` (
  `characterid` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `name` varchar(50) NOT NULL COMMENT 'Character''s previous name',
  PRIMARY KEY (`characterid`,`name`),
  FULLTEXT KEY `name` (`name`),
  CONSTRAINT `char_names_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past names used by characters' `PAGE_COMPRESSED`='ON';