USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__linkshell` (
  `ls_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)',
  `name` varchar(50) NOT NULL COMMENT 'Linkshell name',
  `server_id` tinyint(2) unsigned DEFAULT NULL COMMENT 'ID of the server Linkshell resides on',
  `crossworld` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag indicating whether linkshell is crossworld',
  `formed` datetime(6) DEFAULT NULL COMMENT 'Linkshell formation day as seen on Lodestone',
  `registered` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When Linkshsell was initially added to tracker',
  `updated` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'When Linkshsell was last updated on the tracker',
  `deleted` datetime(6) DEFAULT NULL COMMENT 'Date when Linkshell was marked as deleted',
  `community_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'Community ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/community_finder/communityid/)',
  PRIMARY KEY (`ls_id`) USING BTREE,
  KEY `serverid_ls` (`server_id`),
  KEY `registered` (`registered`),
  KEY `deleted` (`deleted`),
  KEY `crossworld` (`crossworld`),
  KEY `communityid` (`community_id`),
  KEY `name_order` (`name`),
  KEY `updated` (`updated` DESC),
  FULLTEXT KEY `name` (`name`),
  CONSTRAINT `serverid_ls` FOREIGN KEY (`server_id`) REFERENCES `ffxiv__server` (`server_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Linkshells (both crossworld and not) found on Lodestone' `PAGE_COMPRESSED`='ON';