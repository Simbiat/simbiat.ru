USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__linkshell_names` (
  `linkshellid` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)',
  `name` varchar(50) NOT NULL COMMENT 'Previous Linkshell name',
  PRIMARY KEY (`linkshellid`,`name`),
  FULLTEXT KEY `name` (`name`),
  CONSTRAINT `ls_names_id` FOREIGN KEY (`linkshellid`) REFERENCES `ffxiv__linkshell` (`linkshellid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past names of linkshells' `PAGE_COMPRESSED`='ON';