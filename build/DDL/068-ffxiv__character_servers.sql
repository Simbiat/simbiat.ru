CREATE TABLE IF NOT EXISTS `ffxiv__character_servers` (
  `characterid` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `serverid` tinyint(2) unsigned NOT NULL COMMENT 'ID of the server character previously resided on',
  PRIMARY KEY (`characterid`,`serverid`),
  KEY `char_serv_serv` (`serverid`),
  CONSTRAINT `char_serv_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `char_serv_serv` FOREIGN KEY (`serverid`) REFERENCES `ffxiv__server` (`serverid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past servers used by characters' `PAGE_COMPRESSED`='ON';