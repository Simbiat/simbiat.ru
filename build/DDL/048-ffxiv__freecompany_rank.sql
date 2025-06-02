USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__freecompany_rank` (
  `freecompanyid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
  `rankid` tinyint(2) unsigned NOT NULL COMMENT 'ID calculated based on rank icon on Lodestone',
  `rankname` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Name of the rank as reported by Lodestone',
  PRIMARY KEY (`freecompanyid`,`rankid`),
  KEY `rankid` (`rankid`),
  CONSTRAINT `fcranks_freecompany` FOREIGN KEY (`freecompanyid`) REFERENCES `ffxiv__freecompany` (`freecompanyid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Rank names used by companies' `PAGE_COMPRESSED`='ON';