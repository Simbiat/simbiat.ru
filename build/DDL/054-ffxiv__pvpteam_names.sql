USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__pvpteam_names` (
  `pvp_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)',
  `name` varchar(50) NOT NULL COMMENT 'Previous PvP Team name',
  PRIMARY KEY (`pvp_id`,`name`),
  FULLTEXT KEY `name` (`name`),
  CONSTRAINT `pvp_name_id` FOREIGN KEY (`pvp_id`) REFERENCES `ffxiv__pvpteam` (`pvp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Past names of PvP teams' `PAGE_COMPRESSED`='ON';