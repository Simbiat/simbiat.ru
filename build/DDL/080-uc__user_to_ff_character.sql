USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__user_to_ff_character` (
  `userid` int(10) unsigned NOT NULL COMMENT 'User ID',
  `characterid` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  PRIMARY KEY (`characterid`),
  KEY `userid` (`userid`) USING BTREE,
  CONSTRAINT `user_to_ff_char` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userid_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';