CREATE TABLE `ffxiv__freecompany_ranking` (
  `freecompanyid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
  `date` date NOT NULL DEFAULT current_timestamp() COMMENT 'Date of the ranking as identified by tracker',
  `weekly` smallint(3) unsigned NOT NULL DEFAULT 500 COMMENT 'Weekly ranking as reported by Lodestone',
  `monthly` smallint(3) unsigned NOT NULL DEFAULT 500 COMMENT 'Monthly ranking as reported by Lodestone',
  `members` smallint(3) unsigned NOT NULL DEFAULT 1 COMMENT 'Number of registered members at the date of rank update',
  PRIMARY KEY (`freecompanyid`,`date`),
  KEY `date` (`date` DESC),
  CONSTRAINT `fc_ranking_id` FOREIGN KEY (`freecompanyid`) REFERENCES `ffxiv__freecompany` (`freecompanyid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Companies'' weekly and monthly rankings linked to members count' `PAGE_COMPRESSED`='ON';