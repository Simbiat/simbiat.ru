USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__freecompany_names` (
  `fc_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
  `name` varchar(50) NOT NULL COMMENT 'Previous name of the company',
  PRIMARY KEY (`fc_id`,`name`),
  FULLTEXT KEY `name` (`name`),
  CONSTRAINT `fc_names_id` FOREIGN KEY (`fc_id`) REFERENCES `ffxiv__freecompany` (`fc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past names of the companies' `PAGE_COMPRESSED`='ON';