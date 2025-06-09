USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__character_jobs` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `job_id` tinyint(3) unsigned NOT NULL COMMENT 'Job ID',
  `level` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Job level',
  `last_change` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'Last known level change',
  PRIMARY KEY (`character_id`,`job_id`),
  KEY `jobs_to_jobs` (`job_id`),
  CONSTRAINT `jobs_to_character` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jobs_to_jobs` FOREIGN KEY (`job_id`) REFERENCES `ffxiv__jobs` (`job_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Current job levels for characters' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;