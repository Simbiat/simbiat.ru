CREATE TABLE IF NOT EXISTS `ffxiv__orderby` (
  `orderID` tinyint(1) unsigned NOT NULL COMMENT 'ID based on filters from Lodestone',
  `Description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Description of the order',
  PRIMARY KEY (`orderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='ORDER BY options for searches on Lodestone' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;