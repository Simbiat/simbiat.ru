USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__pvpteam` (
  `pvpteamid` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)',
  `name` varchar(50) NOT NULL COMMENT 'PvP Team name',
  `datacenterid` tinyint(2) unsigned DEFAULT NULL COMMENT 'ID of the server PvP Team resides on',
  `formed` date DEFAULT NULL COMMENT 'PvP Team formation day as seen on Lodestone',
  `registered` date NOT NULL DEFAULT current_timestamp() COMMENT 'When PvP Team was initially added to tracker',
  `updated` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'When PvP Team was last updated on the tracker',
  `deleted` date DEFAULT NULL COMMENT 'Date when PvP Team was marked as deleted',
  `communityid` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'Community ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/community_finder/communityid/)',
  `crest_part_1` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'Link to 1st part of the crest (background)',
  `crest_part_2` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'Link to 2nd part of the crest (frame)',
  `crest_part_3` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'Link to 3rd part of the crest (emblem)',
  PRIMARY KEY (`pvpteamid`),
  KEY `pvp_dcid` (`datacenterid`),
  KEY `registered` (`registered`),
  KEY `deleted` (`deleted`),
  KEY `communityid` (`communityid`),
  KEY `name_order` (`name`),
  KEY `updated` (`updated` DESC),
  FULLTEXT KEY `name` (`name`),
  CONSTRAINT `pvp_dcid` FOREIGN KEY (`datacenterid`) REFERENCES `ffxiv__server` (`serverid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='PvP Teams found on Lodestone' `PAGE_COMPRESSED`='ON';