USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_servers` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `server_id` tinyint(2) unsigned NOT NULL COMMENT 'ID of the server character previously resided on',
  PRIMARY KEY (`character_id`,`server_id`),
  KEY `char_serv_serv` (`server_id`),
  CONSTRAINT `char_serv_id` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `char_serv_serv` FOREIGN KEY (`server_id`) REFERENCES `ffxiv__server` (`server_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past servers used by characters' `PAGE_COMPRESSED`='ON';