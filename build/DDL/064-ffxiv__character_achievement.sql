USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_achievement` (
  `characterid` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `achievementid` smallint(5) unsigned NOT NULL COMMENT 'Achievement ID taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/character/characterid/achievement/detail/achievementid/)',
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'Time when achievement was received according to Lodestone',
  PRIMARY KEY (`characterid`,`achievementid`) USING BTREE,
  KEY `ach` (`achievementid`) USING BTREE,
  KEY `time` (`time` DESC),
  CONSTRAINT `char_ach_ach` FOREIGN KEY (`achievementid`) REFERENCES `ffxiv__achievement` (`achievementid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `char_ach_char` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Achievements linked to known characters' `PAGE_COMPRESSED`='ON';