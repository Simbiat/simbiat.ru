CREATE TABLE `ffxiv__grandcompany` (
  `gcId` tinyint(1) unsigned NOT NULL COMMENT 'ID based on filters from Lodestone',
  `gcName` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Name of the company',
  PRIMARY KEY (`gcId`),
  UNIQUE KEY `gcName` (`gcName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='Grand Companies as per lore' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;