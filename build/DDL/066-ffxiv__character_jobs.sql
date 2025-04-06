CREATE TABLE IF NOT EXISTS `ffxiv__character_jobs` (
  `characterid` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `jobid` tinyint(3) unsigned NOT NULL COMMENT 'Job ID',
  `level` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Job level',
  `last_change` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'Last known level change',
  PRIMARY KEY (`characterid`,`jobid`),
  KEY `jobs_to_jobs` (`jobid`),
  CONSTRAINT `jobs_to_character` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jobs_to_jobs` FOREIGN KEY (`jobid`) REFERENCES `ffxiv__jobs` (`jobid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_as_cs COMMENT='Current job levels for characters' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;