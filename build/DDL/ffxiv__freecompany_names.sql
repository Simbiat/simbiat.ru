CREATE TABLE `ffxiv__freecompany_names` (
  `freecompanyid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
  `name` varchar(50) NOT NULL COMMENT 'Previous name of the company',
  PRIMARY KEY (`freecompanyid`,`name`),
  FULLTEXT KEY `name` (`name`),
  CONSTRAINT `fc_names_id` FOREIGN KEY (`freecompanyid`) REFERENCES `ffxiv__freecompany` (`freecompanyid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past names of the companies' `PAGE_COMPRESSED`='ON';