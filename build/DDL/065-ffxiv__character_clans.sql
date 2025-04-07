USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_clans` (
  `characterid` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `genderid` tinyint(1) unsigned NOT NULL COMMENT '0 for female and 1 for male',
  `clanid` tinyint(2) unsigned NOT NULL COMMENT 'Clan ID identifying both clan and race of the character',
  PRIMARY KEY (`characterid`,`genderid`,`clanid`),
  KEY `char_clan_clan` (`clanid`),
  CONSTRAINT `char_clan_clan` FOREIGN KEY (`clanid`) REFERENCES `ffxiv__clan` (`clanid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `char_clan_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past clans used by characters' `PAGE_COMPRESSED`='ON';