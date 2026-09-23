USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__user_to_ff_character` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'User ID',
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  PRIMARY KEY (`character_id`),
  KEY `userid` (`user_id`) USING BTREE,
  CONSTRAINT `user_to_ff_char` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userid_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';