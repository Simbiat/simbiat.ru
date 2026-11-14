USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_threads` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `thread_id` int(10) unsigned NOT NULL COMMENT 'Thread ID',
  PRIMARY KEY (`character_id`,`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Threads related to specific characters' `PAGE_COMPRESSED`='ON';